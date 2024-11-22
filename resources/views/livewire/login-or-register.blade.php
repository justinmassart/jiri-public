<div class="login-or-register">
    <x-slot:title>
        {!! __('title.jiri') !!}

    </x-slot:title>
    <x-slot:page_title>
        {!! __('title.jiri') !!}
    </x-slot:page_title>
    @livewire('login-form')
    @livewire('register-form')
</div>
