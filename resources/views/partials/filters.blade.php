<section class="hb-panel" aria-labelledby="hb-filters-heading">
    <h2 id="hb-filters-heading">Filters</h2>

    <form method="GET" action="{{ route('hookbox-ui.messages.index') }}" class="hb-form">
        <div class="hb-field">
            <label for="hb-source">Source</label>
            <input id="hb-source" type="text" name="source" value="{{ $page['filters']['sourceSlug'] ?? '' }}">
        </div>

        <div class="hb-field">
            <label for="hb-signature-status">Signature Status</label>
            <input id="hb-signature-status" type="text" name="signature_status" value="{{ $page['filters']['signatureStatus'] ?? '' }}">
        </div>

        <div class="hb-field">
            <label for="hb-event-type">Event Type</label>
            <input id="hb-event-type" type="text" name="event_type" value="{{ $page['filters']['eventType'] ?? '' }}">
        </div>

        <div class="hb-field">
            <label for="hb-received-from">Received From</label>
            <input id="hb-received-from" type="text" name="received_from" value="{{ $page['filters']['receivedFrom'] ?? '' }}">
        </div>

        <div class="hb-field">
            <label for="hb-received-to">Received To</label>
            <input id="hb-received-to" type="text" name="received_to" value="{{ $page['filters']['receivedTo'] ?? '' }}">
        </div>

        <div class="hb-field">
            <label for="hb-message-reference">Message Reference</label>
            <input id="hb-message-reference" type="text" name="message_reference" value="{{ $page['filters']['messageReference'] ?? '' }}">
        </div>

        <div class="hb-field">
            <label for="hb-metrics-from">Metrics From</label>
            <input id="hb-metrics-from" type="text" name="metrics_from" value="{{ $page['filters']['metricsFrom'] ?? '' }}">
        </div>

        <div class="hb-field">
            <label for="hb-metrics-to">Metrics To</label>
            <input id="hb-metrics-to" type="text" name="metrics_to" value="{{ $page['filters']['metricsTo'] ?? '' }}">
        </div>

        <div class="hb-field">
            <label for="hb-per-page">Per Page</label>
            <input id="hb-per-page" type="number" min="1" name="per_page" value="{{ $page['pagination']['perPage'] }}">
        </div>

        <div class="hb-actions">
            <button type="submit" class="hb-button">Apply Filters</button>
            <a href="{{ route('hookbox-ui.messages.index') }}" class="hb-button hb-button-secondary">Reset</a>
        </div>
    </form>
</section>
