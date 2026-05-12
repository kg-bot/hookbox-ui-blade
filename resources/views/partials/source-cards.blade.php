<section class="hb-grid hb-grid-sources" aria-label="Source cards">
    @foreach ($sources as $source)
        <article class="hb-card">
            <div class="hb-source-card-header">
                <div class="hb-source-card-copy">
                    <p class="hb-kicker">Source</p>
                    <h2 class="hb-source-card-title">{{ $source['name'] }}</h2>
                    <p class="hb-meta hb-source-card-slug">{{ $source['slug'] }}</p>
                </div>
                <x-hookbox-ui-blade::status-badge :status="$source['isActive'] ? 'active' : 'inactive'" />
            </div>
            <p class="hb-meta">Messages: {{ $source['messages'] }}</p>
            <p class="hb-meta">Attempts: {{ $source['attempts'] }}</p>
        </article>
    @endforeach
</section>
