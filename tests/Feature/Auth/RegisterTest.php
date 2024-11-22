<?php

namespace Tests\Feature\Auth;

use App\Livewire\RegisterForm;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('ensures that a non-authenticated user see the login-or-register view with the register-form component when visiting the app', function () {
    $response = get('/');

    $response
        ->assertOk()
        ->assertSeeLivewire('register-form');
});

it('ensures that a authenticated user cannot see the login-or-register view with the register-form component when visiting the app', function () {
    $user = User::factory()->create();
    $response = actingAs($user)->get('/');

    $response
        ->assertRedirect('/dashboard')
        ->assertDontSeeLivewire('register-form');
});

it('ensures that a user can register itself via the register-form component', function () {
    $response = get('/');

    $response
        ->assertOk()
        ->assertSeeLivewire('register-form');

    Livewire::test(RegisterForm::class)
        ->set('firstname', 'Justin')
        ->assertSet('firstname', 'Justin')
        ->set('lastname', 'Massart')
        ->assertSet('lastname', 'Massart')
        ->set('email', 'justin@massart.be')
        ->assertSet('email', 'justin@massart.be')
        ->set('password', 'password')
        ->assertSet('password', 'password')
        ->set('confirmPassword', 'password')
        ->assertSet('confirmPassword', 'password')
        ->call('register');

    $users = User::all();

    expect($users)
        ->toHaveCount(1);

    $user = $users->first();
    expect($user)
        ->toBeAuthenticated()
        ->firstname->toBe('Justin')
        ->lastname->toBe('Massart')
        ->email->toBe('justin@massart.be');

    $check_password = Hash::check('password', $user->password);
    expect($check_password)
        ->toBeTrue();
});

it('ensures that a non-authenticated user cannot register itself with wrong data', function () {
    $response = get('/');

    $response
        ->assertOk()
        ->assertSeeLivewire('register-form');

    // wrong data following rules in Livewire/RegisterForm
    Livewire::test(RegisterForm::class)
        ->set('firstname', 'Justin1')
        ->assertSet('firstname', 'Justin1')
        ->set('lastname', 'Massart&')
        ->assertSet('lastname', 'Massart&')
        ->set('email', 'justin.be')
        ->assertSet('email', 'justin.be')
        ->set('password', 'pass')
        ->assertSet('password', 'pass')
        ->set('confirmPassword', 'passe')
        ->assertSet('confirmPassword', 'passe')
        ->call('register')
        ->assertHasErrors('firstname', 'lastname', 'email', 'password', 'confirmPassword');

    $users = User::all();

    expect($users)
        ->toHaveCount(0);
});

it('ensures that a non-authenticated user cannot register itself with a already taken email', function () {
    $response = get('/');

    $response
        ->assertOk()
        ->assertSeeLivewire('register-form');

    $user = User::factory()->create();

    Livewire::test(RegisterForm::class)
        ->set('firstname', 'Justin')
        ->assertSet('firstname', 'Justin')
        ->set('lastname', 'Massart')
        ->assertSet('lastname', 'Massart')
        ->set('email', $user->email)
        ->assertSet('email', $user->email)
        ->set('password', 'password')
        ->assertSet('password', 'password')
        ->set('confirmPassword', 'password')
        ->assertSet('confirmPassword', 'password')
        ->call('register')
        ->assertHasErrors('email');

    $users = User::all();

    expect($users)
        ->toHaveCount(1);
});

it('ensures that a authenticated user is forbidden to register a new account', function () {
    $user = User::factory()->create();
    auth()->login($user);

    $response = actingAs($user)->get('/');

    $response
        ->assertRedirect('/dashboard')
        ->assertDontSeeLivewire('register-form');

    Livewire::test(RegisterForm::class)
        ->set('firstname', 'Justin')
        ->assertSet('firstname', 'Justin')
        ->set('lastname', 'Massart')
        ->assertSet('lastname', 'Massart')
        ->set('email', 'justin@massart.be')
        ->assertSet('email', 'justin@massart.be')
        ->set('password', 'password')
        ->assertSet('password', 'password')
        ->set('confirmPassword', 'password')
        ->assertSet('confirmPassword', 'password')
        ->call('register')
        ->assertForbidden();

    $users = User::all();

    expect($users)
        ->toHaveCount(1);
});
