<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class Navigation extends Component
{
    public function logout(User $user)
    {
        if (auth()->user()->id !== $user->id) {
            return abort(403);
        }

        auth()->logout();

        return $this->redirect(LoginOrRegister::class, navigate: true);
    }

    public function render()
    {
        return view('livewire.navigation');
    }
}
