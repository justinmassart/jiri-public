<div class="modal__content">
    <div class="modal__content__container">
        <div class="flex-inline-sb">
            <h2 class="modal__title">{!! __('title.recover_password') !!}</h2>
            <span wire:click.prevent="resetForm; open = false;" class="modal__exit">X</span>
        </div>
        @if ($formStep === 1)
            <form class="form">
                <x-input :type="'email'" :labelFor="'recover-password'" :name="'email'" :wireModel="'email'" :required=true />
                <div class="form__btn btn">
                    <button class="form__btn btn" type="submit" wire:click.prevent="sendPasswordRecoveryToken"
                        wire:loading.attr="disabled">
                        <a><span>{!! __('button.send_reset_password_mail') !!}
                            </span></a>
                    </button>
                </div>
            </form>
        @endif
        @if ($formStep === 2)
            <form class="form">
                <span>{!! __('title.enter_recover_code') !!}
                </span>
                <x-input :type="'text'" :labelFor="'recover-password'" :name="'token'" :wireModel="'token'" :required=true />
                <div class="form__btn btn">
                    <button class="form__btn btn" type="submit" wire:click.prevent="resendRecoverPasswordToken;"
                        wire:loading.attr="disabled">
                        <a><span>{!! __('button.resend_reset_password_mail') !!}
                            </span></a>
                    </button>
                </div>
                <div class="form__btn btn">
                    <button class="form__btn btn" type="submit" wire:click.prevent="usePasswordRecoveryToken"
                        wire:loading.attr="disabled">
                        <a><span>{!! __('button.validate') !!}
                            </span></a>
                    </button>
                </div>
            </form>
        @endif
        @if ($formStep === 3)
            <form class="form">
                <x-input :type="'password'" :labelFor="'new-password'" :name="'newPassword'" :wireModel="'newPassword'" :required=true />
                <x-input :type="'password'" :labelFor="'confirm-new-password'" :name="'confirmedNewPassword'" :wireModel="'confirmedNewPassword'" :required=true />
                <div class="form__btn btn">
                    <button class="form__btn btn" type="submit" wire:click.prevent="resetUserPassword"
                        wire:loading.attr="disabled">
                        <a><span>{!! __('button.validate') !!}
                            </span></a>
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>
