<?php

declare(strict_types=1);

namespace Hookbox\UiBlade\Tests\Feature\Authorization;

use Hookbox\Contracts\WebhookAction;
use Hookbox\UiBlade\Tests\Concerns\CreatesHookboxRecords;
use Hookbox\UiBlade\Tests\TestCase;
use Hookbox\WebhookActionContext;
use Hookbox\WebhookActionRegistry;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;

final class HookboxAuthorizationTest extends TestCase
{
    use CreatesHookboxRecords;
    use DatabaseMigrations;

    protected function afterRefreshingDatabase(): void
    {
        $this->loadHookboxMigrations();
    }

    public function test_view_hookbox_inbox_gates_the_inbox_and_message_pages(): void
    {
        Gate::define('viewHookboxInbox', static fn (?object $user = null): bool => false);
        Gate::define('viewRedactedPayload', static fn (?object $user = null): bool => false);
        Gate::define('replayHookboxMessage', static fn (?object $user = null): bool => true);

        $sourceId = $this->createSource('testing', 'Testing Source');
        $messageId = $this->createMessage(
            sourceId: $sourceId,
            idempotencyKey: 'evt_auth',
            eventType: 'invoice.created',
            signatureStatus: 'valid',
            receivedAt: Carbon::parse('2026-05-10 12:00:00'),
        );

        $this->get('/hookbox/messages')->assertForbidden();
        $this->get('/hookbox/messages/'.$messageId)->assertForbidden();
    }

    public function test_replay_hookbox_message_hides_the_form_and_forbids_post_requests(): void
    {
        Gate::define('viewHookboxInbox', static fn (?object $user = null): bool => true);
        Gate::define('viewRedactedPayload', static fn (?object $user = null): bool => false);
        Gate::define('replayHookboxMessage', static fn (?object $user = null): bool => false);

        $messageId = $this->createReplayableMessage();

        $this->get('/hookbox/messages/'.$messageId)
            ->assertOk()
            ->assertDontSee('Replay message')
            ->assertDontSee('name="triggered_by"', false)
            ->assertDontSee('name="force_reverify"', false)
            ->assertDontSee('name="live_replay"', false);

        $this->post('/hookbox/messages/'.$messageId.'/replay')->assertForbidden();
    }

    private function createReplayableMessage(): string
    {
        $sourceId = $this->createSource('testing', 'Testing Source');
        $messageId = $this->createMessage(
            sourceId: $sourceId,
            idempotencyKey: 'evt_replay_auth',
            eventType: 'invoice.created',
            signatureStatus: 'valid',
            receivedAt: Carbon::parse('2026-05-10 12:00:00'),
        );

        $this->appInstance()->make(WebhookActionRegistry::class)
            ->handle('testing')
            ->when(eventType: 'invoice.created')
            ->through(AuthorizationReplayAction::class);

        return $messageId;
    }
}

final class AuthorizationReplayAction implements WebhookAction
{
    public function handle(WebhookActionContext $context, \Closure $next): mixed
    {
        return $next($context);
    }
}
