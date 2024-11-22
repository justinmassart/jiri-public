<?php

namespace App\Livewire\Notifications;

use Livewire\Attributes\On;
use Livewire\Component;

class Popup extends Component
{
    public $message = '';

    public $alertType = '';

    public $showPopup = false;

    #[On('notify')]
    public function popup($data)
    {
        if (empty($data['message'])) {
            return;
        }

        $this->message = $data['message'];
        $this->alertType = $data['alertType'] ?? 'notify';

        $this->dispatch('displayPopup');

        $this->showPopup = true;
    }

    #[On('resetPopup')]
    public function resetPopup()
    {
        $this->message = '';
        $this->alertType = '';
        $this->showPopup = false;
    }

    public function render()
    {
        return view('livewire.notifications.popup');
    }
}
