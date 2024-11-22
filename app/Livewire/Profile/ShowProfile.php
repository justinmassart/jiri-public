<?php

namespace App\Livewire\Profile;

use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ShowProfile extends Component
{
    protected $layout = 'components.layouts.app';

    #[Validate(['required', 'string', 'min:2', 'max:50', 'regex:/^[A-Za-z\-]+$/'])]
    public $firstname = '';

    #[Validate(['required', 'string', 'min:2', 'max:50', 'regex:/^[A-Za-z\-]+$/'])]
    public $lastname = '';

    #[Validate(['required', 'email'])]
    public $email = '';

    #[Validate(['required', 'current_password', 'min:8', 'max:50'])]
    public $oldPassword = '';

    #[Validate(['required', 'string', 'min:8', 'max:50', 'different:oldPassword'])]
    public $newPassword = '';

    #[Validate(['required', 'string', 'min:8', 'max:50', 'same:newPassword', 'different:oldPassword'])]
    public $confirmNewPassword = '';

    public $showResetPasswordModal = false;

    public $showDeleteProfileModal = false;

    public function toggleResetPasswordModal()
    {
        $this->showResetPasswordModal = ! $this->showResetPasswordModal;
    }

    public function toggleDeleteProfileModal()
    {
        $this->showDeleteProfileModal = ! $this->showDeleteProfileModal;
    }

    public function saveProfile()
    {
        $validated = $this->validate([
            'firstname' => 'required|string|min:2|max:50|regex:/^[A-Za-z\-]+$/',
            'lastname' => 'required|string|min:2|max:50|regex:/^[A-Za-z\-]+$/',
            'email' => 'required|email|unique:users,email,'.auth()->user()->id,
        ]);

        try {
            DB::beginTransaction();

            auth()->user()->update([
                'firstname' => $validated['firstname'],
                'lastname' => $validated['lastname'],
                'email' => $validated['email'],
            ]);

            DB::commit();

            $this->dispatch('notify', ['message' => __('popup.profile_updated'), 'alertType' => 'success']);
        } catch (\Throwable $th) {
            DB::rollBack();

            $this->dispatch('notify', ['message' => __('popup.'.$th->getMessage()), 'alertType' => 'error']);

            return;
        }
    }

    public function resetPassword()
    {
        $validated = $this->validate([
            'oldPassword' => 'required|current_password|min:8|max:50',
            'newPassword' => 'required|string|min:8|max:50|different:oldPassword',
            'confirmNewPassword' => 'required|string|min:8|max:50|same:newPassword|different:oldPassword',
        ]);

        try {
            DB::beginTransaction();

            auth()->user()->update([
                'password' => bcrypt($validated['newPassword']),
            ]);

            DB::commit();

            $this->dispatch('notify', ['message' => __('popup.password_updated'), 'alertType' => 'success']);

            $this->toggleResetPasswordModal();
        } catch (\Throwable $th) {
            DB::rollBack();

            $this->dispatch('notify', ['message' => __('popup.'.$th->getMessage()), 'alertType' => 'error']);

            return;
        }
    }

    public function deleteProfile()
    {
        try {
            DB::beginTransaction();

            auth()->user()->delete();

            DB::commit();

            $this->dispatch('notify', ['message' => __('popup.profile_deleted'), 'alertType' => 'success']);

            $this->toggleDeleteProfileModal();

            auth()->logout();

            return redirect()->route('home');
        } catch (\Throwable $th) {
            DB::rollBack();

            $this->dispatch('notify', ['message' => __('popup.'.$th->getMessage()), 'alertType' => 'error']);

            return;
        }
    }

    public function mount()
    {
        $this->firstname = auth()->user()->firstname;
        $this->lastname = auth()->user()->lastname;
        $this->email = auth()->user()->email;
    }

    public function render()
    {
        return view('livewire.profile.show-profile')->layout($this->layout);
    }
}
