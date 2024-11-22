<div>
    <x-slot:title>
        {!! __('title.dashboard') . ' | Jiri' !!}
    </x-slot:title>
    <x-slot:page_title>
        {!! __('title.dashboard') !!}
    </x-slot:page_title>
    <h2>Hello {!! auth()->guard('contact')->user()->firstname !!}</h2>
</div>
