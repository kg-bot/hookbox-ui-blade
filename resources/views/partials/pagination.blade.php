@if (($pagination['lastPage'] ?? 1) > 1)
    <nav class="hb-pagination" aria-label="Pagination">
        <p class="hb-meta">Page {{ $pagination['currentPage'] }} of {{ $pagination['lastPage'] }}</p>

        <div class="hb-pagination-links">
            @for ($pageNumber = 1; $pageNumber <= $pagination['lastPage']; $pageNumber++)
                <a
                    href="{{ request()->fullUrlWithQuery(['page' => $pageNumber]) }}"
                    class="hb-page-link"
                    @if ($pageNumber === $pagination['currentPage']) aria-current="page" @endif
                >
                    {{ $pageNumber }}
                </a>
            @endfor
        </div>
    </nav>
@endif
