<div class="input">
    <label for="{!! $labelFor . '-' . $name !!}">{!! __('input.' . $name) !!}</label>
    <input @error('{!! $name !!}')class="error"@enderror type="{!! $type !!}" {!! $type === 'number' ? 'min="0" max="1" step="0.01"' : '' !!}
        name="{!! $name !!}" id="{!! $labelFor . '-' . $name !!}" placeholder="{!! __('input.' . $name . '_placeholder') !!}"
        {!! $required ? 'required ' : '' !!} {!! $wireModel
            ? ($type === 'search'
                ? 'wire:model.live.debounce.100ms="' . $wireModel . '"'
                : ($type === 'file'
                    ? 'wire:model="' . $wireModel . '"'
                    : 'wire:model.blur="' . $wireModel . '"'))
            : '' !!} {!! $labelFor === 'login' ? 'autofocus' : '' !!} autocomplete="off"
        {!! $type === 'file' && $wireModel === 'picture' ? 'accept="image/png, image/jpeg, image/jpg, image/gif"' : '' !!}>
    <x-input-error :inputName="$name" />
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('contact-picture')) {
            document.getElementById('contact-picture').addEventListener('change', function() {
                var fileSize = this.files[0].size / 10240 / 10240;
                if (fileSize > 2) {
                    alert('Le poids du fichier ne peut être supérieur à 5MB');
                    this.value = null;
                }
            });
            Livewire.on('removePicture', () => {
                document.getElementById('contact-picture').value = null;
            })
        };
        if (document.getElementById('editContact-picture')) {
            document.getElementById('editContact-picture').addEventListener('change', function() {
                var fileSize = this.files[0].size / 10240 / 10240;
                if (fileSize > 2) {
                    alert('Le poids du fichier ne peut être supérieur à 10MB');
                    this.value = null;
                }
            });
            Livewire.on('removePicture', () => {
                document.getElementById('editContact-picture').value = null;
            })
        };
    });
</script>
