<div class="contacts" wire:init="notify">
    <x-slot:title>
        {!! __('title.contacts') . ' | Jiri' !!}
    </x-slot:title>
    <x-slot:page_title>
        {!! __('title.contacts') !!}
    </x-slot:page_title>
    @if (auth()->user()->contacts->isNotEmpty())
        <div class="sorts__actions card">
            <div class="sorts__actions__filters">
                <div class="sorts__actions__filters__sort" x-data="{ open: false }">
                    <div class="sorts__actions__filters__sort__btn">
                        <x-button-modal :type="'button'" :translationKey="'sort'" :displayBehavior=true />
                    </div>
                    <div class="sorts__actions__filters__sort__modal filter-modal" x-show="open"
                        @click.away="open = false" style="display: none">
                        <span class="filter-modal__title">{!! __('title.sort_by') !!}
                        </span>
                        <div class="filter-modal__sort">
                            <h3>{!! __('input.firstname') !!} : </h3>
                            <div>
                                <span wire:click.prevent="setSort('firstname', 'asc')">&uarr;</span>
                                <span wire:click.prevent="setSort('firstname', 'desc')">&darr;</span>
                            </div>
                        </div>
                        <div class="filter-modal__sort">
                            <h3>{!! __('input.lastname') !!} : </h3>
                            <div>
                                <span wire:click.prevent="setSort('lastname', 'asc')">&uarr;</span>
                                <span wire:click.prevent="setSort('lastname', 'desc')">&darr;</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="sorts__actions__filters__search{!! $search ? '-locked' : '' !!}">
                    <x-input :type="'search'" :labelFor="'contact'" :name="'search'" :wireModel="'search'" :required=false />
                </div>
            </div>
            <div class="contacts-action__add-contact">
                <div>
                    <button class="form__btn btn" type="button" wire:click.prevent="toggleAddContactModal">
                        <a><span>{!! __('button.add_contact') !!}</span></a>
                    </button>
                </div>
            </div>
        </div>
        @if ($this->contacts->isNotEmpty())
            <div class="main__list" x-data="{ open: '{!! $showModal !!}' }">
                @foreach ($this->contacts as $contact)
                    <div class="main__list__item" wire:key="{!! $contact->id !!}">
                        <a class="main__list__item__link link"
                            wire:click.prevent="setSelectedContact('{!! $contact->slug !!}'); open = true;"
                            tabindex="0"><span class="hidden">{!! __('button.go_to_profile') . $contact->firstname . ' ' . $contact->lastname !!}</span></a>
                        <div class="main__list__item__infos">
                            <h3>{!! $contact->firstname . ' ' . $contact->lastname !!}</h3>
                            <p>{!! $contact->email !!}</p>
                        </div>
                        <div class="main__list__item__picture">
                            <img width="100" height="100" src="{!! asset($contact->image->image_url) . '?' . $contact->image->updated_at->format('U') ?? '' !!}"
                                alt="{!! __('button.image_of') . $contact->firstname . ' ' . $contact->lastname !!}">
                        </div>
                    </div>
                @endforeach
                @if ($selectedContact)
                    <div class="main__modal modal" x-show="open">
                        @livewire('contacts.show-contact', ['contact' => $selectedContact])
                    </div>
                @endif
            </div>
            {{ $this->contacts->links() }}
        @else
            <div class="not-found card">
                <h3>{!! __('title.search_not_found', ['search' => $search]) !!}</h3>
            </div>
        @endif
    @elseif (
        $this->contacts->isEmpty() &&
            auth()->user()->contacts->isEmpty())
        <div class="not-found card">
            <h3>{!! __('title.no_contacts') !!}</h3>
            <div class="contacts-action__add-contact">
                <div class="mt-2">
                    <button class="form__btn btn" type="button" wire:click.prevent="toggleAddContactModal">
                        <a><span>{!! __('button.add_contact') !!}</span></a>
                    </button>
                </div>
            </div>
        </div>
    @endif
    @if ($showAddContactModal)
        <div class="contacts__modal modal">
            @livewire('contacts.add-contact-modal')
        </div>
    @endif
</div>
