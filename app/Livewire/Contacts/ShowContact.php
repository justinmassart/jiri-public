<?php

namespace App\Livewire\Contacts;

use App\Models\Contact;
use App\Models\ContactImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\ImageManager;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class ShowContact extends Component
{
    use WithFileUploads;

    protected $layout = 'components.layouts.app';

    public $title = 'Contacts | Jiri';

    public $page_title = 'Contacts';

    public $contact;

    #[Validate(['required', 'min:2', 'max:255', 'string', 'regex:/^[A-Za-z\-]+$/'])]
    public $firstname = '';

    #[Validate(['required', 'min:2', 'max:255', 'string', 'regex:/^[A-Za-z\-]+$/'])]
    public $lastname = '';

    #[Validate(['required', 'email'])]
    public $email = '';

    #[Validate(['nullable', 'image', 'max:10240'])]
    public $picture;

    public bool $showDeleteContactModal = false;

    public function updateContact()
    {
        $validated = $this->validate();

        try {
            DB::beginTransaction();

            $this->contact->update([
                'firstname' => $this->formatInput($this->firstname),
                'lastname' => $this->formatInput($this->lastname),
                'email' => strtolower($this->email),
                'slug' => Str::slug($this->firstname.' '.$this->lastname),
            ]);

            if ($this->picture) {
                $this->validate([
                    'picture' => ['nullable', 'image', 'mimes:gif,jpeg,jpg,png', 'max:10240'],
                ]);

                $filename = Str::slug($validated['firstname'].'-'.$validated['lastname']).'-'.$this->contact->id.'.jpg';

                $manager = new ImageManager(
                    new GdDriver()
                );

                $file_exists = file_exists(storage_path('app/public/livewire-tmp/'.$this->picture->getFilename()));

                if (! $file_exists) {
                    throw new \Exception('temporary_file_not_found_error');
                }

                $image = $manager->read(storage_path('app/public/livewire-tmp/'.$this->picture->getFilename()));

                $image->scale(300, 300);

                $encodedImage = $image->toJpeg(75);

                $encodedImage->save(storage_path('app/public/avatars/'.$filename));

                $contactImage = ContactImage::updateOrCreate(['contact_id' => $this->contact->id], [
                    'contact_id' => $this->contact->id,
                    'image_url' => 'avatars/'.$filename,
                    'updated_at' => now(),
                ]);

                if (! $contactImage) {
                    throw new \Exception('save_image_error');
                }

                unlink(storage_path('app/public/livewire-tmp/'.$this->picture->getFilename()));
            }

            DB::commit();

            $this->picture = null;

            $this->resetSelectedContact();

            $this->dispatch('notify', ['message' => __('popup.update_contact_success', ['firstname' => $this->firstname, 'lastname' => $this->lastname]), 'alertType' => 'success']);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function deleteContact()
    {
        try {
            DB::beginTransaction();

            $this->contact->delete();

            DB::commit();

            $this->redirect(Contacts::class, navigate: true);
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function removePicture()
    {
        if ($this->picture) {
            $this->picture = null;

            return;
        }

        try {
            DB::beginTransaction();

            $this->contact->image()->update([
                'image_url' => 'avatars/default-avatar.jpg',
            ]);

            DB::commit();

            $this->dispatch('removePicture');

            $this->dispatch('notify', ['message' => __('popup.image_removed_success'), 'alertType' => 'success']);
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->dispatch('notify', ['message' => __('popup.'.$th->getMessage()), 'alertType' => 'error']);

            throw $th;
        }
    }

    public function toggleShowDeleteContactModal()
    {
        $this->showDeleteContactModal = ! $this->showDeleteContactModal;
    }

    protected function formatInput($input)
    {
        $input = strtolower($input);
        $parts = explode('-', $input);
        $parts = array_map('ucfirst', $parts);

        return implode('-', $parts);
    }

    public function resetForm()
    {
        $this->reset();
    }

    public function mount(Contact $contact = null)
    {
        if (! $contact) {
            return;
        }

        $this->contact = $contact->load('image');

        $this->firstname = $contact->firstname ?? '';
        $this->lastname = $contact->lastname ?? '';
        $this->email = $contact->email ?? '';
        $this->picture = null;
    }

    public function resetSelectedContact()
    {
        $this->dispatch('refreshComponent');
        $this->dispatch('toggleShowContactModal');
        $this->dispatch('resetSelectedContact');
    }

    public function render()
    {
        return view('livewire.contacts.show-contact')->layout($this->layout);
    }
}
