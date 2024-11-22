<?php

namespace App\Livewire\Evaluator;

use App\Livewire\LoginOrRegister;
use Auth;
use Livewire\Component;

class LogoutButton extends Component
{
    public function logout()
    {
        Auth::guard('contact')->logout();

        return $this->redirect(LoginOrRegister::class, navigate: true);
    }

    public function render()
    {
        return view('livewire.evaluator.logout-button');
    }
}
