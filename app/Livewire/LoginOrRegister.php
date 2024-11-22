<?php

namespace App\Livewire;

use Livewire\Component;

class LoginOrRegister extends Component
{
    protected $layout = 'components.layouts.guest';

    public $title = 'Se connecter ou créer votre compte | Jiri';

    public $page_title = 'Jiri';

    public function mount()
    {
        auth()->check() ? $this->redirect(Home::class, navigate: true) : null;
    }

    public function render()
    {
        return view('livewire.login-or-register')->layout($this->layout);
    }
}
