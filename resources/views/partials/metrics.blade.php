<section class="hb-grid hb-grid-metrics" aria-label="Inbox metrics">
    <article class="hb-card">
        <p class="hb-kicker">Valid</p>
        <p class="hb-value">{{ $metrics['messages']['valid'] }}</p>
        <p class="hb-meta">Messages</p>
    </article>
    <article class="hb-card">
        <p class="hb-kicker">Invalid</p>
        <p class="hb-value">{{ $metrics['messages']['invalid'] }}</p>
        <p class="hb-meta">Messages</p>
    </article>
    <article class="hb-card">
        <p class="hb-kicker">Skipped</p>
        <p class="hb-value">{{ $metrics['messages']['skipped'] }}</p>
        <p class="hb-meta">Messages</p>
    </article>
    <article class="hb-card">
        <p class="hb-kicker">Pending</p>
        <p class="hb-value">{{ $metrics['attempts']['pending'] }}</p>
        <p class="hb-meta">Attempts</p>
    </article>
    <article class="hb-card">
        <p class="hb-kicker">Succeeded</p>
        <p class="hb-value">{{ $metrics['attempts']['succeeded'] }}</p>
        <p class="hb-meta">Attempts</p>
    </article>
    <article class="hb-card">
        <p class="hb-kicker">Failed</p>
        <p class="hb-value">{{ $metrics['attempts']['failed'] }}</p>
        <p class="hb-meta">Attempts</p>
    </article>
    <article class="hb-card">
        <p class="hb-kicker">Skipped</p>
        <p class="hb-value">{{ $metrics['attempts']['skipped'] }}</p>
        <p class="hb-meta">Attempts</p>
    </article>
</section>
