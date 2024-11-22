<?php

namespace App\Livewire;

use App\Events\RecoveredPasswordEvent;
use App\Models\RecoverPassword;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Livewire\Component;

class RecoverPasswordModal extends Component
{
    #[Validate(['required', 'email', 'exists:users,email'])]
    public $email;

    public $token;

    public $newPassword;

    public $confirmedNewPassword;

    public $formStep = 1;

    public function sendPasswordRecoveryToken()
    {
        try {
            $validated = $this->validate();

            $user = User::whereEmail($validated['email'])->first();

            if ($user->recover_password()->first() && Carbon::parse($user->recover_password()->first()->expires_at)->isPast() === false) {
                $this->formStep = 2;

                return;
            }

            DB::beginTransaction();

            $recover_password = $user->recover_password()->create([
                'token' => Str::random(6),
                'expires_at' => now()->addMinutes(30),
            ]);

            if (! $recover_password) {
                $this->addError('error', 'Oups, une erreur est survenue.');

                return;
            }

            DB::commit();

            $mail = RecoveredPasswordEvent::dispatch($recover_password);

            if (! $mail) {
                throw new \Exception('mail_not_sent');
            }

            $this->dispatch('notify', ['message' => __('popup.recover_password_mail_sent'), 'alertType' => 'success']);

            $this->formStep = 2;
        } catch (\Throwable $th) {
            DB::rollback();
            $this->dispatch('notify', ['message' => __('popup.'.$th->getMessage()), 'alertType' => 'error']);

            return;
        }
    }

    public function resendRecoverPasswordToken()
    {
        try {

            $user = User::whereEmail($this->email)->first();

            if (! $user->recover_password()->first()) {
                $this->sendPasswordRecoveryToken();

                return;
            }

            $user->recover_password()->first()->delete();

            $this->sendPasswordRecoveryToken();
        } catch (\Throwable $th) {
            throw $th;

            return;
        }
    }

    public function usePasswordRecoveryToken()
    {
        try {
            $validated = $this->validate([
                'token' => 'required|min:6|exists:recover_passwords,token',
            ]);

            $user = User::whereEmail($this->email)->first();

            $recover_password = RecoverPassword::whereToken($validated['token'])->first();

            if (! $recover_password) {
                $this->addError('token', 'Ce code de récupération n’existe pas.');

                return;
            }

            if ($recover_password->user_id !== $user->id) {
                abort(403);

                return;
            }

            if ($recover_password->token !== $user->recover_password()->first()->token) {
                $this->addError('token', 'Le code de récupération est incorrecte.');

                return;
            }

            if (Carbon::parse($recover_password->expires_at)->isPast()) {
                $this->addError('token', 'Le code de récupération a expiré.');

                return;
            }

            DB::beginTransaction();

            $recover_password->delete();

            DB::commit();

            $this->formStep = 3;
        } catch (\Throwable $th) {
            DB::rollback();
            $this->dispatch('notify', ['message' => __('popup.'.$th->getMessage()), 'alertType' => 'error']);

            return;
        }
    }

    public function resetUserPassword()
    {
        try {

            $validated = $this->validate([
                'newPassword' => 'required|min:8',
                'confirmedNewPassword' => 'required|min:8|same:newPassword',
            ]);

            $user = User::whereEmail($this->email)->first();

            if (Hash::check($validated['newPassword'], $user->password)) {
                $this->addError('newPassword', 'Le nouveau mot de passe doit être différent de l’ancien.');

                return;
            }

            DB::beginTransaction();

            $newPassword = bcrypt($validated['newPassword']);

            $user->update([
                'password' => $newPassword,
            ]);

            DB::commit();

            $this->resetForm();

            $this->redirect(LoginOrRegister::class, navigate: true);

            //TODO: Create SendNewPasswordNotificationEmail to user
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;

            return;
        }
    }

    public function updatedRecoverPasswordToken()
    {
        $this->validate([
            'token' => 'required|min:6',
        ]);
    }

    public function updatedNewPassword()
    {
        $this->validate([
            'newPassword' => 'required|min:8',
        ]);
    }

    public function updatedConfirmedNewPassword()
    {
        $this->validate([
            'confirmedNewPassword' => 'required|min:8|same:newPassword',
        ]);
    }

    public function resetForm()
    {
        $this->email = '';
        $this->token = '';
        $this->newPassword = '';
        $this->confirmedNewPassword = '';
        $this->formStep = 1;
    }

    public function render()
    {
        return view('livewire.recover-password-modal');
    }
}
