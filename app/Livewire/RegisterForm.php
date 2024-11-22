<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Attributes\Validate;
use Livewire\Component;

class RegisterForm extends Component
{
    #[Validate(['required', 'string', 'min:2', 'max:50', 'regex:/^[A-Za-z\-]+$/'])]
    public $firstname;

    #[Validate(['required', 'string', 'min:2', 'max:50', 'regex:/^[A-Za-z\-]+$/'])]
    public $lastname;

    #[Validate(['required', 'email'])]
    public $email;

    #[Validate(['required', 'string', 'min:8', 'max:50'])]
    public $password;

    #[Validate(['required', 'string', 'min:8', 'max:50', 'same:password'])]
    public $confirmPassword;

    public function register()
    {
        if (auth()->check()) {
            return;
        }

        try {
            $validated = $this->validate();

            $user_data = [
                'firstname' => $validated['firstname'],
                'lastname' => $validated['lastname'],
                'email' => $validated['email'],
                'password' => bcrypt($validated['password']),
            ];

            $user = User::create($user_data);

            if (! $user) {
                throw new \Exception('user_not_created');
            }

            session()->put('welcome', true);

            auth()->login($user);

            $this->redirect(Home::class, navigate: true);
        } catch (\Throwable $th) {
            $this->dispatch('notify', ['message' => __('popup.'.$th->getMessage()), 'alertType' => 'error']);

            return;
        }
    }

    public function render()
    {
        return view('livewire.register-form');
    }
}
