<header class="app__header header">
    <div class="header__title">
        <h1>{{ $page_title }}</h1>
    </div>
    @auth
        @livewire('home-button')
    @endauth
</header>
