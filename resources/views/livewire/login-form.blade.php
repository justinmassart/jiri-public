<div class="login" x-data="{
    open: false,
}">
    <div x-show="open" class="modal" style="display: none">
        @livewire('recover-password-modal')
    </div>
    <div class="login-form form-container">
        <x-container-title :translationKey="'login'" />
        <form class="form" wire:submit.prevent='login'>
            <x-input :type="'email'" :labelFor="'login'" :name="'email'" :wireModel="'email'" :required=true />
            <x-input :type="'password'" :labelFor="'login'" :name="'password'" :wireModel="'password'" :required=true />
            <div class="login-form__btn">
                <x-button :type="'submit'" :translationKey="'login'" />
                <x-button-modal :type="'button'" :translationKey="'forgot_password'" :displayBehavior="'true'" />
            </div>
        </form>
    </div>
</div>
