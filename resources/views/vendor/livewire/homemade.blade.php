<div class="pagination">
    @if ($paginator->hasPages())
        <nav class="pagination__nav">
            <ul class="pagination__nav__list">
                @foreach ($elements as $element)
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="pagination__nav__list__item current" aria-current="page" tabindex="0">
                                    <span class="hidden">page = {{ $page }}</span>
                                </li>
                            @else
                                <li class="pagination__nav__list__item" wire:click="gotoPage({{ $page }});"
                                    @click="scrollTo({top: 0, behavior: 'smooth'})" tabindex="0" wire:model.live="page"
                                    aria-label="@lang('pagination.goto_page', ['page' => $page])">
                                    <span class="hidden">page = {{ $page }}</span>
                                </li>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </ul>
        </nav>
    @endif
</div>
