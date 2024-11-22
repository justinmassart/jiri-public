<div class="profile">
    <x-slot:title>
        {!! __('title.profile') . ' | Jiri' !!}
    </x-slot:title>
    <x-slot:page_title>
        {!! __('title.profile') !!}
    </x-slot:page_title>
    <div class="profile__form card">
        <form class="form" wire:submit.prevent="saveProfile">
            <x-input :type="'text'" :labelFor="'profile'" :name="'firstname'" :wireModel="'firstname'" :required=true />
            <x-input :type="'text'" :labelFor="'profile'" :name="'lastname'" :wireModel="'lastname'" :required=true />
            <x-input :type="'email'" :labelFor="'profile'" :name="'email'" :wireModel="'email'" :required=true />
            <div class="flex-inline-sb mt-2">
                <button class="form__btn btn" type="button" wire:click.prevent="toggleResetPasswordModal;"
                    wire:loading.attr="disabled">
                    <a><span>{!! __('button.reset_password') !!}
                        </span></a>
                </button>
                <x-button :type="'submit'" :translationKey="'save'" />
            </div>
            <button class="form__btn btn" type="button" wire:click.prevent="toggleDeleteProfileModal;"
                wire:loading.attr="disabled">
                <a><span>{!! __('button.delete_profile') !!}
                    </span></a>
            </button>
        </form>
    </div>
    @if ($showResetPasswordModal)
        <div class="modal">
            <div class="modal__content" @click.away="toggleResetPasswordModal">
                <div class="modal__content__container">
                    <div class="flex-inline-sb">
                        <h2 class="modal__title">{!! __('title.recover_password') !!}
                        </h2>
                        <span wire:click.prevent="toggleResetPasswordModal" class="modal__exit">X</span>
                    </div>
                    <form class="form" wire:submit.prevent="resetPassword">
                        <x-input :type="'password'" :labelFor="'profile'" :name="'old_password'" :wireModel="'oldPassword'"
                            :required=true />
                        <x-input :type="'password'" :labelFor="'profile'" :name="'new_password'" :wireModel="'newPassword'"
                            :required=true />
                        <x-input :type="'password'" :labelFor="'profile'" :name="'confirm_new_password'" :wireModel="'confirmNewPassword'"
                            :required=true />
                        <div class="mt-2">
                            <x-button :type="'submit'" :translationKey="'reset_password'" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
    @if ($showDeleteProfileModal)
        <div class="modal">
            <div class="modal__content" @click.away="toggleDeleteProfileModal">
                <div class="modal__content__container">
                    <div class="flex-inline-sb">
                        <h2 class="modal__title">{!! __('title.delete_profile') !!}
                        </h2>
                        <span wire:click.prevent="toggleDeleteProfileModal" class="modal__exit">X</span>
                    </div>
                    <p class="fz-20">{!! __('title.delete_profile_message') !!}</p>
                    <form class="form" wire:submit.prevent="deleteProfile">
                        <div class="flex-inline-sb-mt">
                            <button class="form__btn btn-red" type="submit">
                                <a><span>{!! __('button.delete_confirm') !!}
                                    </span></a>
                            </button>
                            <x-button :type="'button'" :translationKey="'cancel'" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
