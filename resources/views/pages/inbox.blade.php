<x-hookbox-ui-blade::layout :title="$title" :brand="$brand">
    @include('hookbox-ui-blade::partials.flash')
    @include('hookbox-ui-blade::partials.metrics', ['metrics' => $page['metrics']])
    @include('hookbox-ui-blade::partials.source-cards', ['sources' => $page['sources']])
    @include('hookbox-ui-blade::partials.filters', ['page' => $page])

    @if ($page['rows'] === [])
        <p class="hb-empty">No Hookbox messages matched the current filters.</p>
    @else
        @include('hookbox-ui-blade::partials.messages-table', ['rows' => $page['rows']])
        @include('hookbox-ui-blade::partials.pagination', ['pagination' => $page['pagination']])
    @endif
</x-hookbox-ui-blade::layout>
