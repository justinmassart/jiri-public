<header class="app__header header">
    <div class="header__title">
        <h1>{{ $page_title }}</h1>
    </div>
    @if (Auth::guard('contact')->check() &&
            url()->current() === route('evaluator.dashboard', ['jiri' => session()->get('jiri')->slug]))
    @endif
    @auth
        @livewire('home-button')
    @endauth
</header>
