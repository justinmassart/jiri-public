<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    @vite(['resources/css/app.css'])
    @livewireStyles
</head>

<body class="app">
    @include('components.header-guest')
    <main class="app__main main">
        {{ $slot }}
    </main>
    @livewire('notifications.popup')
    @livewireScripts
</body>

</html>
