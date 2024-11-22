<div class="jiris">
    <x-slot:title>
        {!! __('title.jiris') . ' | Jiri' !!}
    </x-slot:title>
    <x-slot:page_title>
        {!! __('title.jiris') !!}
    </x-slot:page_title>
    @if (auth()->user()->jiris->isNotEmpty())
        <div class="sorts__actions card">
            <div class="sorts__actions__filters">
                <div class="sorts__actions__filters__sort" x-data="{ open: false }">
                    <div class="sorts__actions__filters__sort__btn">
                        <x-button-modal :type="'button'" :translationKey="'sort'" :displayBehavior=true />
                    </div>
                    <div class="sorts__actions__filters__sort__modal filter-modal" x-show="open"
                        @click.away="open = false" style="display: none">
                        <span class="filter-modal__title">{!! __('button.sort') !!}
                        </span>
                        <div class="filter-modal__sort">
                            <h3>{!! __('input.creation_date') !!}</h3>
                            <div>
                                <span wire:click.prevent="setSort('starts_at', 'asc')">&uarr;</span>
                                <span wire:click.prevent="setSort('starts_at', 'desc')">&darr;</span>
                            </div>
                        </div>
                        <div class="filter-modal__sort">
                            <h3>{!! __('input.finish_date') !!}</h3>
                            <div>
                                <span wire:click.prevent="setSort('ends_at', 'asc')">&uarr;</span>
                                <span wire:click.prevent="setSort('ends_at', 'desc')">&darr;</span>
                            </div>
                        </div>
                        <div class="filter-modal__sort">
                            <h3>{!! __('input.name') !!}</h3>
                            <div>
                                <span wire:click.prevent="setSort('name', 'asc')">&uarr;</span>
                                <span wire:click.prevent="setSort('name', 'desc')">&darr;</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="sorts__actions__filters__search{!! $search ? '-locked' : '' !!}">
                    <x-input :type="'search'" :labelFor="'contact'" :name="'search'" :wireModel="'search'" :required=true />
                </div>
            </div>
            <div class="jiris-action__add-contact">
                <div>
                    <button class="form__btn btn" type="button" wire:click.prevent="toggleAddJiriModal">
                        <a><span>{!! __('button.add_jiri') !!}</span></a>
                    </button>
                </div>
                @if ($showModal)
                    <div class="jiris__modal modal">
                        @livewire('jiris.add-jiri-modal')
                    </div>
                @endif
            </div>
        </div>
        @if ($this->jiris->isNotEmpty())
            <div class="main__list" x-data="{ open: '{!! $showModal !!}' }">
                @foreach ($this->jiris as $jiri)
                    <div class="main__list__item" wire:key="{!! $jiri->id !!}">
                        <a class="main__list__item__link"
                            wire:click.prevent="setSelectedJiri('{!! $jiri->slug !!}'); open = true;"
                            tabindex="0"><span class="hidden">{!! __('button.go_to_profile') . $jiri->firstname . ' ' . $jiri->lastname !!}</span></a>
                        <div class="main__list__item__infos">
                            <h3>{!! $jiri->name !!}</h3>
                            <p class="mt-2">{!! $jiri->attendances->count() . ' ' . __('title.participants') !!}</p>
                            <p class="mt-2">{!! __('title.created_at', ['date' => date('d/m/Y', strtotime($jiri->starts_at))]) !!}</p>
                        </div>
                    </div>
                @endforeach
                @if ($selectedJiri)
                    <div class="jiris__modal show__jiri__modal modal" x-show="open" style="display:none">
                        @livewire('jiris.show-jiri', ['jiri' => $selectedJiri])
                    </div>
                @endif
            </div>
            {{ $this->jiris->links() }}
        @else
            <div class="not-found card">
                <h3>{!! __('title.search_not_found', ['search' => $search]) !!}</h3>
            </div>
        @endif
    @else
        <div class="not-found card">
            <h3>{!! __('title.no_jiris') !!}</h3>
            <div class="mt-2">
                <button class="form__btn btn" type="button" wire:click.prevent="toggleAddJiriModal">
                    <a><span>{!! __('button.add_jiri') !!}</span></a>
                </button>
            </div>
        </div>
        @if ($showModal)
            <div class="jiris__modal modal">
                @livewire('jiris.add-jiri-modal')
            </div>
        @endif
    @endif
</div>
