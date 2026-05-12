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

final class MixedModeReplayControllerTest extends TestCase
{
    use CreatesHookboxRecords;
    use DatabaseMigrations;

    /**
     * @var array<string, mixed>
     */
    protected array $hookboxUiConfigOverrides = ['enabled' => true];

    protected function afterRefreshingDatabase(): void
    {
        $this->loadHookboxMigrations();
    }

    public function test_replay_post_keeps_the_blade_html_flow_when_ui_core_routes_are_enabled(): void
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
        $response->assertSessionHas('hookbox-ui-blade.replay.handler', MixedModeTestReplayAction::class);
        $response->assertSessionHas('hookbox-ui-blade.replay.triggeredBy', 'blade-ui');
    }

    private function createReplayableMessage(): string
    {
        $sourceId = $this->createSource('testing', 'Testing');
        $messageId = $this->createMessage(
            sourceId: $sourceId,
            idempotencyKey: 'evt_replayable_mixed_mode',
            eventType: 'invoice.created',
            signatureStatus: 'valid',
            receivedAt: Carbon::parse('2026-05-12 10:00:00'),
        );

        $this->appInstance()->make(WebhookActionRegistry::class)
            ->handle('testing')
            ->when(eventType: 'invoice.created')
            ->through(MixedModeTestReplayAction::class);

        return $messageId;
    }
}

final class MixedModeTestReplayAction implements WebhookAction
{
    public function handle(WebhookActionContext $context, \Closure $next): mixed
    {
        return $next($context);
    }
}
