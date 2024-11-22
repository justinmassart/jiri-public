<div class="modal__content">
    <div class="modal__content__container" x-data="{ open: false }">
        <div class="modal-title-exit">
            <div class="flex-inline-sb">
                <h2 class="modal__title">{!! $contact->firstname . ' ' . $contact->lastname !!}</h2>
                <span wire:click.prevent="resetSelectedContact; open = false;" class="modal__exit">X</span>
            </div>
        </div>
        <form class="form" wire:submit.prevent="updateContact; open = false;" x-show="!open">
            <div class="editContact-form__name">
                <x-input :type="'text'" :labelFor="'editContact'" :name="'firstname'" :wireModel="'firstname'" :required=true />
                <x-input :type="'text'" :labelFor="'editContact'" :name="'lastname'" :wireModel="'lastname'" :required=true />
            </div>
            <x-input :type="'email'" :labelFor="'editContact'" :name="'email'" :wireModel="'email'" :required=true />
            <div class="input__picture relative" @click="$refs.fileInput.click()">
                <input wire:model.live="picture" type="file" class="hidden" x-ref="fileInput">
                <label>Avatar</label>
                <div>
                    <svg version="1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48"
                        enable-background="new 0 0 48 48" width="100px"
                        style="border: 1px solid black; border-radius: 1rem">
                        <rect width="100%" height="100%" fill="white" />
                        <polygon fill="#9AC9E3" points="20,16 9,32 31,32" />
                        <polygon fill="#B3DDF5" points="31,22 23,32 39,32" />
                        <g fill="#0074c2">
                            <rect x="36" y="32" width="4" height="12" />
                            <rect x="32" y="36" width="12" height="4" />
                        </g>
                    </svg>
                    @if ($picture)
                        <div>
                            <img src="{{ $picture->temporaryUrl() }}" width="150">
                        </div>
                    @else
                        <div>
                            <img src="{{ asset($contact->image->image_url) . '?' . $contact->image->updated_at->format('U') ?? '' }}"
                                width="150">
                        </div>
                    @endif
                </div>
            </div>
            @if ($picture)
                <button class="btn-red" type="button" wire:click.prevent="removePicture">
                    <a><span>{!! __('button.delete') !!}</span></a>
                </button>
            @endif
            <div class="flex-inline-sb-mt">
                <div class="update-form__btn">
                    <x-button :type="'submit'" :translationKey="'save'" />
                </div>
                <div class="delete-form__btn">
                    <button class="btn-red" type="button" wire:click.prevent="toggleShowDeleteContactModal">
                        <a><span>{!! __('button.delete') !!}</span></a>
                    </button>
                </div>
            </div>
        </form>
    </div>
    @if ($showDeleteContactModal)
        <div class="modal">
            <div class="modal__over">
                <div class="flex-inline-sb">
                    <h3 class="modal__title">{!! __('title.delete_contact', ['contact' => $contact->firstname . ' ' . $contact->lastname]) !!}
                    </h3>
                    <div class="modal-title-exit">
                        <span wire:click.prevent="toggleShowDeleteContactModal" class="modal__exit">X</span>
                    </div>
                </div>
                <form class="form" wire:submit.prevent="deleteContact">
                    <span>Voulez vous vraiment supprimer {!! $contact->firstname . ' ' . $contact->lastname !!} ?</span>
                    <div class="flex-inline-sb-mt">
                        <div class="delete-form__btn">
                            <button class="btn-red" type="submit">
                                <a><span>{!! __('button.confirm') !!}</span></a>
                            </button>
                        </div>
                        <div class="delete-form__btn">
                            <button class="btn" type="button" wire:click.prevent="toggleShowDeleteContactModal">
                                <a><span>{!! __('button.cancel') !!}</span></a>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
