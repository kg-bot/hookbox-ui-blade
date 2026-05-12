<x-hookbox-ui-blade::layout :title="$title" :brand="$brand">
    <p><a href="{{ route('hookbox-ui.messages.index') }}">Back to inbox</a></p>

    @include('hookbox-ui-blade::partials.flash')

    @if ($page['message']['isRedacted'])
        <div class="hb-panel hb-notice" role="status">
            <p class="hb-meta">Message payload has been redacted.</p>

            @unless ($page['abilities']['viewRedactedPayload'])
                <p class="hb-meta">You do not have permission to view redacted payloads.</p>
            @endunless
        </div>
    @endif

    <section class="hb-panel" aria-labelledby="hb-message-summary-title">
        <h2 id="hb-message-summary-title">Message summary</h2>

        <div class="hb-grid hb-grid-summary">
            <div>
                <p class="hb-kicker">Message ID</p>
                <p class="hb-meta">{{ $page['message']['id'] }}</p>
            </div>
            <div>
                <p class="hb-kicker">Source</p>
                <p class="hb-meta">{{ $page['message']['sourceName'] ?? $page['message']['sourceSlug'] ?? 'Unknown' }}</p>
            </div>
            <div>
                <p class="hb-kicker">Event Type</p>
                <p class="hb-meta">{{ $page['message']['eventType'] ?? 'Unknown' }}</p>
            </div>
            <div>
                <p class="hb-kicker">Message Reference</p>
                <p class="hb-meta">{{ $page['message']['idempotencyKey'] ?? 'Unknown' }}</p>
            </div>
            <div>
                <p class="hb-kicker">Signature Status</p>
                <p><x-hookbox-ui-blade::status-badge :status="$page['message']['signatureStatus']" /></p>
            </div>
            <div>
                <p class="hb-kicker">Received At</p>
                <p class="hb-meta">{{ $page['message']['receivedAt'] }}</p>
            </div>
            <div>
                <p class="hb-kicker">Client IP</p>
                <p class="hb-meta">{{ $page['message']['clientIp'] ?? 'Unknown' }}</p>
            </div>
            <div>
                <p class="hb-kicker">Redacted</p>
                <p class="hb-meta">{{ $page['message']['isRedacted'] ? 'Yes' : 'No' }}</p>
            </div>
        </div>
    </section>

    @include('hookbox-ui-blade::partials.replay-form', ['page' => $page])
    @include('hookbox-ui-blade::partials.attempts-table', ['attempts' => $page['attempts']])
</x-hookbox-ui-blade::layout>
