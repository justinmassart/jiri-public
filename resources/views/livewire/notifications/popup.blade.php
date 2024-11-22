<div class="popup-root">
    @if ($showPopup)
        <div class="popup show">
            <div class="popup__container">
                <div class="alert alert-{!! $alertType !!}" role="alert" id="message-alert"
                    wire:click.prevent="resetPopup">
                    <span class="alert__message">{!! $message !!}</span>
                    <svg class="popup-anim" width="65" height="65">
                        <circle class="start-animation" cx="30" cy="30" r="25"></circle>
                    </svg>
                </div>
            </div>
        </div>
    @endif
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Livewire.on('displayPopup', (data) => {
                setTimeout(() => {
                    document.querySelector('.popup').classList.remove('show');
                    document.querySelector('.popup').classList.add('fade-out');
                    setTimeout(() => {
                        Livewire.dispatch('resetPopup');
                    }, 1000);
                }, 3500);
            });
        });
    </script>
</div>
