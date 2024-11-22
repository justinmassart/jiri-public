<?php

namespace Tests\Feature\Popup;

use App\Livewire\LoginForm;
use App\Models\User;
use Livewire\Livewire;

it('ensures that a popup is created when user logged himself to welcome him', function () {
    $user = User::factory()->create();

    Livewire::test(LoginForm::class)
        ->set('email', $user->email)
        ->set('password', 'password')
        ->call('login')
        ->assertHasNoErrors()
        ->assertDispatched('notify');
});
