<?php

namespace Tests\Feature\Mail;

use App\Jobs\SendRecoveredPasswordMailJob;
use App\Livewire\RecoverPasswordModal;
use App\Mail\RecoveredPasswordMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;

it('ensures that a guest user can recover its password', function () {
    Queue::fake();
    Mail::fake();

    $user = User::factory()->create();

    Livewire::test(RecoverPasswordModal::class)
        ->set('email', $user->email)
        ->assertSet('email', $user->email)
        ->call('sendPasswordRecoveryToken');

    $this->assertDatabaseHas('recover_passwords', [
        'user_id' => $user->id,
    ]);

    Queue::assertPushed(function (SendRecoveredPasswordMailJob $job) use ($user) {
        return $job->password->user->id === $user->id;
    });

    Mail::assertSent(RecoveredPasswordMail::class, function ($mail) use ($user) {
        return $mail->hasTo($user->email);
    });
});
