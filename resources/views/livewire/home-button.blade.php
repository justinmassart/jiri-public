    <div class="header__home">
        <a class="header__link" href="{!! Auth::guard('contact') ? '/dashboard' : '/' !!}" wire:navigate title="Retourner vers la page d'accueil"><span
                class="hidden">Dashboard</span></a>
        <img class="header__svg" src="{{ asset('storage/svg/Home.svg') }}" width="20" height="20" alt="">
    </div>
