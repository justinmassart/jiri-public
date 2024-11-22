<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Validate;
use Livewire\Component;

class LoginForm extends Component
{
    public $title = 'Se connecter ou créer un compte | Jiri';

    #[Validate('required|email|exists:users,email')]
    public $email;

    #[Validate('required|min:8')]
    public $password;

    public function login()
    {
        try {
            $validated = $this->validate();

            $user = User::whereEmail($validated['email'])->first();

            if (! $user) {
                $this->addError('email', 'user_not_found');
                throw new \Exception('user_not_found');
            }

            $check_passwords = Hash::check($validated['password'], $user->password);

            if (! $check_passwords) {
                $this->addError('password', 'password_not_match');
                throw new \Exception('password_not_match');
            }

            Auth::guard('web')->login($user);

            session()->put('welcome', 'user');

            return $this->redirect(Home::class, navigate: true);
        } catch (\Throwable $th) {
            $this->dispatch('notify', ['message' => __('popup.'.$th->getMessage()), 'alertType' => 'error']);

            return;
        }
    }

    public function render()
    {
        return view('livewire.login-form');
    }
}
