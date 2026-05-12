<?php

declare(strict_types=1);

namespace Hookbox\UiBlade\Tests\Feature\Http;

use Hookbox\UiBlade\Tests\Concerns\CreatesHookboxRecords;
use Hookbox\UiBlade\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;

final class MessagePageTest extends TestCase
{
    use CreatesHookboxRecords;
    use DatabaseMigrations;

    protected function afterRefreshingDatabase(): void
    {
        $this->loadHookboxMigrations();
    }

    public function test_message_page_renders_message_summary_attempts_and_redaction_notice(): void
    {
        Gate::define('viewHookboxInbox', static fn (?object $user = null): bool => true);
        Gate::define('viewRedactedPayload', static fn (?object $user = null): bool => false);
        Gate::define('replayHookboxMessage', static fn (?object $user = null): bool => true);

        $sourceId = $this->createSource('stripe', 'Stripe');
        $messageId = $this->createMessage(
            sourceId: $sourceId,
            idempotencyKey: 'evt_123',
            eventType: 'invoice.created',
            signatureStatus: 'valid',
            receivedAt: Carbon::parse('2026-05-09 10:00:00'),
            clientIp: '127.0.0.1',
            redactedAt: Carbon::parse('2026-05-09 10:05:00'),
        );

        $this->createAttempt(
            messageId: $messageId,
            kind: 'initial',
            handler: 'FirstHandler',
            status: 'failed',
            startedAt: Carbon::parse('2026-05-09 10:01:00'),
            finishedAt: Carbon::parse('2026-05-09 10:01:05'),
            durationMs: 5000,
            errorClass: 'RuntimeException',
            errorMessage: 'First failure.',
        );

        $this->createAttempt(
            messageId: $messageId,
            kind: 'replay',
            handler: 'SecondHandler',
            status: 'succeeded',
            startedAt: Carbon::parse('2026-05-09 10:05:00'),
            finishedAt: Carbon::parse('2026-05-09 10:05:02'),
            durationMs: 2000,
            triggeredBy: 'blade-ui',
        );

        $response = $this->get('/hookbox/messages/'.$messageId);

        $response->assertOk();
        $response->assertHeader('content-type', 'text/html; charset=UTF-8');
        $response->assertSee('Message details');
        $response->assertSee('href="'.route('hookbox-ui.messages.index').'"', false);
        $response->assertSee($messageId);
        $response->assertSee('Stripe');
        $response->assertSee('invoice.created');
        $response->assertSee('evt_123');
        $response->assertSee('127.0.0.1');
        $response->assertSee('SecondHandler');
        $response->assertSee('First failure.');
        $response->assertSee('Message payload has been redacted.');
        $response->assertSee('You do not have permission to view redacted payloads.');
        $response->assertSee('Dry run is the default replay mode.');
        $response->assertSee('name="triggered_by"', false);
        $response->assertSee('name="live_replay"', false);
        $response->assertSee('name="force_reverify"', false);
        $response->assertSee('action="'.route('hookbox-ui.messages.replay', $messageId).'"', false);
    }

    public function test_message_page_hides_live_replay_controls_when_live_replay_is_disabled(): void
    {
        Gate::define('viewHookboxInbox', static fn (?object $user = null): bool => true);
        Gate::define('viewRedactedPayload', static fn (?object $user = null): bool => false);
        Gate::define('replayHookboxMessage', static fn (?object $user = null): bool => true);

        config()->set('hookbox-ui.replay.allow_live', false);

        $sourceId = $this->createSource('stripe', 'Stripe');
        $messageId = $this->createMessage(
            sourceId: $sourceId,
            idempotencyKey: 'evt_456',
            eventType: 'invoice.created',
            signatureStatus: 'valid',
            receivedAt: Carbon::parse('2026-05-09 11:00:00'),
        );

        $this->get('/hookbox/messages/'.$messageId)
            ->assertOk()
            ->assertSee('Dry run is the only replay mode currently available.')
            ->assertDontSee('name="live_replay"', false);
    }

    public function test_message_page_renders_replay_flash_feedback_from_the_session(): void
    {
        Gate::define('viewHookboxInbox', static fn (?object $user = null): bool => true);
        Gate::define('viewRedactedPayload', static fn (?object $user = null): bool => false);
        Gate::define('replayHookboxMessage', static fn (?object $user = null): bool => true);

        $sourceId = $this->createSource('stripe', 'Stripe');
        $messageId = $this->createMessage(
            sourceId: $sourceId,
            idempotencyKey: 'evt_flash',
            eventType: 'invoice.created',
            signatureStatus: 'valid',
            receivedAt: Carbon::parse('2026-05-09 12:00:00'),
        );

        $this->withSession([
            'hookbox-ui-blade' => [
                'replay' => [
                    'kind' => 'dry_run',
                    'status' => 'succeeded',
                    'handler' => 'Tests\\ReplayHandler',
                    'triggeredBy' => 'blade-ui',
                ],
            ],
        ])->get('/hookbox/messages/'.$messageId)
            ->assertOk()
            ->assertSee('Replay dry_run succeeded via Tests\\ReplayHandler.')
            ->assertSee('Triggered by blade-ui.');
    }
}
