<nav class="nav" x-data="{ open: false }" :class="{ 'nav-open': open }" @click.away="open = false;">
    <div class="nav__content">
        <h2 class="nav__title">{!! __('title.jiri') !!}
        </h2>
        <a class="nav__link{{ Route::currentRouteNamed('dashboard') ? ' current' : '' }}" title="Aller sur le dashboard"
            href="{{ route('dashboard') }}" wire:navigate>{!! __('navigation.dashboard') !!}
        </a>
        <a class="nav__link{{ Route::currentRouteNamed('scores') ? ' current' : '' }}" title="Aller sur la page des cotes"
            href="{{ route('scores') }}" wire:navigate>{!! __('navigation.scores') !!}</a>
        <a class="nav__link{{ Route::currentRouteNamed('contacts') ? ' current' : '' }}"
            title="Aller sur la page des contacts" href="{{ route('contacts') }}"
            wire:navigate>{!! __('navigation.contacts') !!}</a>
        <a class="nav__link{{ Route::currentRouteNamed('jiris') ? ' current' : '' }}"
            title="Aller sur la page des jurys" href="{{ route('jiris') }}" wire:navigate>{!! __('navigation.jurys') !!}</a>
        <a class="nav__link{{ Route::currentRouteNamed('profile') ? ' current' : '' }}"
            title="Aller sur la page de mon profil" href="{{ route('profile') }}"
            wire:navigate>{!! __('navigation.profile') !!}</a>
        <form class="nav__logout" wire:submit.prevent="logout({{ auth()->user()->id }})">
            <button class="nav__btn btn" type="submit">{!! __('button.logout') !!}
            </button>
        </form>
    </div>
    <div class="nav__drawer" @click="open = !open"></div>
</nav>
