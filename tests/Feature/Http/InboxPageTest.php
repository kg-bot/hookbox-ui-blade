<?php

declare(strict_types=1);

namespace Hookbox\UiBlade\Tests\Feature\Http;

use Hookbox\UiBlade\Tests\Concerns\CreatesHookboxRecords;
use Hookbox\UiBlade\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;

final class InboxPageTest extends TestCase
{
    use CreatesHookboxRecords;
    use DatabaseMigrations;

    protected function afterRefreshingDatabase(): void
    {
        $this->loadHookboxMigrations();
    }

    public function test_inbox_page_renders_rows_filters_metrics_and_source_cards(): void
    {
        Gate::define('viewHookboxInbox', static fn (?object $user = null): bool => true);
        Gate::define('viewRedactedPayload', static fn (?object $user = null): bool => false);
        Gate::define('replayHookboxMessage', static fn (?object $user = null): bool => true);

        $sourceId = $this->createSource('alpha', 'Alpha Source');
        $olderMessageId = $this->createMessage(
            sourceId: $sourceId,
            idempotencyKey: 'evt_old',
            eventType: 'invoice.created',
            signatureStatus: 'valid',
            receivedAt: Carbon::parse('2026-05-09 10:00:00'),
        );

        $newerMessageId = $this->createMessage(
            sourceId: $sourceId,
            idempotencyKey: 'evt_new',
            eventType: 'invoice.updated',
            signatureStatus: 'invalid',
            receivedAt: Carbon::parse('2026-05-09 11:00:00'),
            redactedAt: Carbon::parse('2026-05-09 11:05:00'),
        );

        $this->createAttempt(
            messageId: $olderMessageId,
            kind: 'initial',
            handler: 'AlphaPendingHandler',
            status: 'pending',
            startedAt: Carbon::parse('2026-05-09 10:05:00'),
        );

        $this->createAttempt(
            messageId: $olderMessageId,
            kind: 'replay',
            handler: 'AlphaSucceededHandler',
            status: 'succeeded',
            startedAt: Carbon::parse('2026-05-09 10:06:00'),
            finishedAt: Carbon::parse('2026-05-09 10:06:02'),
            durationMs: 2000,
        );

        $response = $this->get('/hookbox/messages?source=alpha&metrics_from=2026-05-09T10:00:00%2B00:00&metrics_to=2026-05-09T12:00:00%2B00:00&per_page=1&page=1');

        $response->assertOk();
        $response->assertHeader('content-type', 'text/html; charset=UTF-8');
        $response->assertSee('Hookbox Inbox');
        $response->assertSee('Hookbox');
        $response->assertSee('Alpha Source');
        $response->assertSee('invoice.updated');
        $response->assertSee('evt_new');
        $response->assertSee('Valid');
        $response->assertSee('Invalid');
        $response->assertSee('Skipped');
        $response->assertSee('Pending');
        $response->assertSee('Succeeded');
        $response->assertSee('name="source"', false);
        $response->assertSee('name="signature_status"', false);
        $response->assertSee('name="event_type"', false);
        $response->assertSee('name="received_from"', false);
        $response->assertSee('name="received_to"', false);
        $response->assertSee('name="message_reference"', false);
        $response->assertSee('name="metrics_from"', false);
        $response->assertSee('name="metrics_to"', false);
        $response->assertSee('name="per_page"', false);
        $response->assertSee('href="'.route('hookbox-ui.messages.show', $newerMessageId).'"', false);
        $response->assertSee('href="'.url('/hookbox/messages?source=alpha&amp;metrics_from=2026-05-09T10%3A00%3A00%2B00%3A00&amp;metrics_to=2026-05-09T12%3A00%3A00%2B00%3A00&amp;per_page=1&amp;page=2').'"', false);
        $response->assertSee('Page 1 of 2');
        $response->assertDontSee('style="', false);
    }

    public function test_inbox_page_shows_an_empty_state_when_no_rows_exist(): void
    {
        Gate::define('viewHookboxInbox', static fn (?object $user = null): bool => true);
        Gate::define('viewRedactedPayload', static fn (?object $user = null): bool => false);
        Gate::define('replayHookboxMessage', static fn (?object $user = null): bool => false);

        $this->get('/hookbox/messages')
            ->assertOk()
            ->assertSee('No Hookbox messages matched the current filters.');
    }
}
