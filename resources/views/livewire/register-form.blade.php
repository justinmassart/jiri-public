<div class="register-form form-container">
    <x-container-title :translationKey="'register'" />
    <form class="form" wire:submit.prevent='register'>
        <div class="flex-col-2">
            <x-input :type="'text'" :labelFor="'register'" :name="'firstname'" :wireModel="'firstname'" :required=true />
            <x-input :type="'text'" :labelFor="'register'" :name="'lastname'" :wireModel="'lastname'" :required=true />
        </div>
        <x-input :type="'email'" :labelFor="'register'" :name="'email'" :wireModel="'email'" :required=true />
        <x-input :type="'password'" :labelFor="'register'" :name="'password'" :wireModel="'password'" :required=true />
        <x-input :type="'password'" :labelFor="'register_confirmation'" :name="'confirmPassword'" :wireModel="'confirmPassword'" :required=true />
        <div class="login-form__btn">
            <x-button :type="'submit'" :translationKey="'register'" />
        </div>
    </form>
</div>
