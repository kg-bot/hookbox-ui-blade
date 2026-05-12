<?php

declare(strict_types=1);

namespace Hookbox\UiBlade\Tests\Feature\Http;

use Hookbox\Contracts\WebhookAction;
use Hookbox\UiBlade\Tests\Concerns\CreatesHookboxRecords;
use Hookbox\UiBlade\Tests\TestCase;
use Hookbox\WebhookActionContext;
use Hookbox\WebhookActionRegistry;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;

final class ReplayControllerTest extends TestCase
{
    use CreatesHookboxRecords;
    use DatabaseMigrations;

    protected function afterRefreshingDatabase(): void
    {
        $this->loadHookboxMigrations();
    }

    public function test_replay_post_redirects_back_with_a_dry_run_flash_by_default(): void
    {
        Gate::define('viewHookboxInbox', static fn (?object $user = null): bool => true);
        Gate::define('viewRedactedPayload', static fn (?object $user = null): bool => false);
        Gate::define('replayHookboxMessage', static fn (?object $user = null): bool => true);

        $messageId = $this->createReplayableMessage();

        $response = $this->from('/hookbox/messages/'.$messageId)
            ->post('/hookbox/messages/'.$messageId.'/replay', [
                'triggered_by' => 'blade-ui',
            ]);

        $response->assertRedirect('/hookbox/messages/'.$messageId);
        $response->assertSessionHas('hookbox-ui-blade.replay.kind', 'dry_run');
        $response->assertSessionHas('hookbox-ui-blade.replay.status', 'succeeded');
        $response->assertSessionHas('hookbox-ui-blade.replay.handler', TestReplayAction::class);
        $response->assertSessionHas('hookbox-ui-blade.replay.triggeredBy', 'blade-ui');
    }

    public function test_live_replay_requires_an_explicit_checkbox_and_enabled_core_config(): void
    {
        Gate::define('viewHookboxInbox', static fn (?object $user = null): bool => true);
        Gate::define('viewRedactedPayload', static fn (?object $user = null): bool => false);
        Gate::define('replayHookboxMessage', static fn (?object $user = null): bool => true);

        $messageId = $this->createReplayableMessage();

        config()->set('hookbox-ui.replay.allow_live', false);

        $this->post('/hookbox/messages/'.$messageId.'/replay', ['live_replay' => '1'])
            ->assertRedirect('/hookbox/messages/'.$messageId)
            ->assertSessionHas('hookbox-ui-blade.replay.kind', 'dry_run');

        config()->set('hookbox-ui.replay.allow_live', true);

        $this->post('/hookbox/messages/'.$messageId.'/replay', ['live_replay' => '1'])
            ->assertRedirect('/hookbox/messages/'.$messageId)
            ->assertSessionHas('hookbox-ui-blade.replay.kind', 'replay');
    }

    public function test_invalid_form_input_redirects_back_with_errors(): void
    {
        Gate::define('viewHookboxInbox', static fn (?object $user = null): bool => true);
        Gate::define('viewRedactedPayload', static fn (?object $user = null): bool => false);
        Gate::define('replayHookboxMessage', static fn (?object $user = null): bool => true);

        $messageId = $this->createReplayableMessage();

        $this->from('/hookbox/messages/'.$messageId)
            ->post('/hookbox/messages/'.$messageId.'/replay', [
                'live_replay' => 'banana',
                'force_reverify' => 'banana',
                'triggered_by' => ['not-a-string'],
            ])
            ->assertRedirect('/hookbox/messages/'.$messageId)
            ->assertSessionHasErrors(['live_replay', 'force_reverify', 'triggered_by']);
    }

    public function test_invalid_triggered_by_old_input_does_not_break_the_message_page(): void
    {
        Gate::define('viewHookboxInbox', static fn (?object $user = null): bool => true);
        Gate::define('viewRedactedPayload', static fn (?object $user = null): bool => false);
        Gate::define('replayHookboxMessage', static fn (?object $user = null): bool => true);

        $messageId = $this->createReplayableMessage('evt_replayable_invalid_old_input');

        $this->from('/hookbox/messages/'.$messageId)
            ->post('/hookbox/messages/'.$messageId.'/replay', [
                'triggered_by' => ['not-a-string'],
            ])
            ->assertRedirect('/hookbox/messages/'.$messageId)
            ->assertSessionHasErrors(['triggered_by']);

        $this->get('/hookbox/messages/'.$messageId)
            ->assertOk()
            ->assertSee('The triggered by field must be a string.');
    }

    private function createReplayableMessage(
        string $idempotencyKey = 'evt_replayable',
        ?Carbon $receivedAt = null,
    ): string {
        $sourceId = $this->createSource('testing', 'Testing');
        $messageId = $this->createMessage(
            sourceId: $sourceId,
            idempotencyKey: $idempotencyKey,
            eventType: 'invoice.created',
            signatureStatus: 'valid',
            receivedAt: $receivedAt ?? Carbon::parse('2026-05-12 10:00:00'),
        );

        $this->appInstance()->make(WebhookActionRegistry::class)
            ->handle('testing')
            ->when(eventType: 'invoice.created')
            ->through(TestReplayAction::class);

        return $messageId;
    }
}

final class TestReplayAction implements WebhookAction
{
    public function handle(WebhookActionContext $context, \Closure $next): mixed
    {
        return $next($context);
    }
}
