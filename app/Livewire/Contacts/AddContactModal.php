<?php

namespace App\Livewire\Contacts;

use App\Models\Attendance;
use App\Models\ContactImage;
use App\Models\Jiri;
use App\Models\JiriProject;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\ImageManager;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Throwable;

class AddContactModal extends Component
{
    use WithFileUploads;

    #[Validate(['required', 'boolean'])]
    public $switchRole = false;

    #[Validate(['required', 'string', 'in:evaluator,student'])]
    public $role = 'evaluator';

    #[Validate(['required', 'alpha', 'string', 'min:2', 'max:50'])]
    public $firstname;

    #[Validate(['required', 'alpha', 'string', 'min:2', 'max:50'])]
    public $lastname;

    #[Validate(['required', 'email'])]
    public $email;

    #[Validate(['nullable', 'image', 'max:10240'])]
    public $picture;

    #[Validate(['integer', 'nullable'])]
    public $jiri;

    #[Validate(['array', 'nullable'])]
    public $projects = [];

    public $jiriProjects = [];

    public $formStep = 1;

    public $contactId;

    public $contactFile;

    public array $importedContacts = [];

    public bool $showImportedContactsModal = false;

    public function createContact(bool $nextStep = false, bool $again = false)
    {
        try {
            $validated = $this->validate();

            $contact_data = [
                'firstname' => $validated['firstname'],
                'lastname' => $validated['lastname'],
                'email' => $validated['email'],
                'slug' => Str::slug($validated['firstname'].' '.$validated['lastname']),
            ];

            DB::beginTransaction();

            $user = User::find(auth()->user()->id);

            $contact = $user->contacts()->updateOrCreate(['id' => $this->contactId], $contact_data);

            if ($this->picture) {
                $this->validate([
                    'picture' => ['nullable', 'image', 'mimes:gif,jpeg,jpg,png', 'max:10240'],
                ]);

                $filename = Str::slug($validated['firstname'].'-'.$validated['lastname']).'-'.$contact->id.'.jpg';

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

                $contactImage = ContactImage::updateOrCreate(['contact_id' => $contact->id], [
                    'contact_id' => $contact->id,
                    'image_url' => 'avatars/'.$filename,
                    'updated_at' => now(),
                ]);

                if (! $contactImage) {
                    throw new \Exception('save_image_error');
                }

                unlink(storage_path('app/public/livewire-tmp/'.$this->picture->getFilename()));
            } else {
                $contactImage = ContactImage::updateOrCreate(['contact_id' => $contact->id], [
                    'contact_id' => $contact->id,
                    'image_url' => 'avatars/default-avatar.jpg',
                    'updated_at' => now(),
                ]);

                if (! $contactImage) {
                    throw new \Exception('save_image_error');
                }
            }

            $this->contactId = $contact->id;

            if (! $contact) {
                throw new \Exception('add_contact_error');
            }

            DB::commit();

            $this->dispatch('contactAdded');

            $contact = $user->contacts()->whereId($this->contactId)->first();

            if (! $this->jiri) {
                $this->dispatch('notify', ['message' => __('popup.add_contact_success', ['firstname' => ucfirst($contact->firstname), 'lastname' => ucfirst($contact->lastname)]), 'alertType' => 'success']);
                $this->dispatch('toggleAddContactModal');
                $this->resetForm();

                return;
            }

            $this->assignToJiri($nextStep, $again);
        } catch (Throwable $th) {
            DB::rollBack();
            $this->dispatch('notify', ['message' => __('popup.'.$th->getMessage()), 'alertType' => 'error']);

            return;
        }
    }

    public function assignToJiri(bool $nextStep = false, bool $again = false)
    {
        try {
            $validated = $this->validate([
                'jiri' => ['required', 'integer'],
                'role' => ['required', 'string', 'in:evaluator,student'],
            ]);

            $user = User::find(auth()->user()->id);

            $contact = $user->contacts()->whereId($this->contactId)->first();

            if ($validated['jiri'] && $validated['role'] === 'evaluator') {
                $jiri = $user->jiris()->whereId($validated['jiri'])->first();

                if (! $jiri) {
                    throw new \Exception('jiri_not_found_error');
                }

                DB::beginTransaction();

                $attendance = $contact->attendances()->create([
                    'jiri_id' => $jiri->id,
                    'role' => $validated['role'],
                ]);

                if (! $attendance) {
                    throw new \Exception('add_contact_to_jiri_error');
                }

                DB::commit();

                $this->dispatch('notify', ['message' => __('popup.add_contact_to_jiri_success', ['firstname' => ucfirst($contact->firstname), 'lastname' => ucfirst($contact->lastname), 'jiri' => $jiri->name]), 'alertType' => 'success']);
            }

            if ($validated['jiri'] && $validated['role'] === 'student') {
                $jiri = Jiri::whereId($validated['jiri'])->first();

                if (! $jiri) {
                    throw new \Exception('jiri_not_found_error');
                }

                DB::beginTransaction();

                $attendance = $contact->attendances()->create([
                    'jiri_id' => $jiri->id,
                    'role' => $validated['role'],
                ]);

                if (! $attendance) {
                    throw new \Exception('add_contact_to_jiri_error');
                }

                DB::commit();
            }

            if ($nextStep) {
                $this->nextStep();

                return;
            }

            if ($again) {
                $this->resetForm();

                return;
            } else {
                $this->dispatch('toggleAddContactModal', true);
                $this->resetForm();
                $this->dispatch('notify', ['message' => __('popup.add_contact_success', ['firstname' => ucfirst($contact->firstname), 'lastname' => ucfirst($contact->lastname)]), 'alertType' => 'success']);

                return;
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->dispatch('notify', ['message' => __('popup.'.$th->getMessage()), 'alertType' => 'error']);

            return;
        }
    }

    public function assignProjects()
    {
        try {
            $validated = $this->validate([
                'jiri' => ['required', 'integer'],
                'role' => ['required', 'string', 'in:evaluator,student'],
                'projects' => ['required', 'array'],
            ]);

            $user = User::find(auth()->user()->id);

            $contact = $user->contacts()->whereId($this->contactId)->first();

            if ($validated['jiri'] && $validated['role'] === 'student') {
                $jiri = Jiri::whereId($validated['jiri'])->first();

                if (! $jiri) {
                    throw new \Exception('jiri_not_found_error');
                }

                $attendance = Attendance::whereContactId($contact->id)->whereJiriId($jiri->id)->first();

                if (! $attendance) {
                    throw new \Exception('add_attendance_error');
                }

                DB::beginTransaction();

                if ($validated['projects']) {
                    foreach ($validated['projects'] as $index => $projects) {
                        $jiriProjectId = JiriProject::whereJiriId($jiri->id)->whereName($index)->first()->id;
                        $newProject = Project::create([
                            'urls' => json_encode($projects),
                            'jiri_project_id' => $jiriProjectId,
                            'contact_id' => $this->contactId,
                        ]);

                        if (! $newProject) {
                            throw new \Exception('add_project_error');
                        }
                    }
                }

                DB::commit();

                $this->dispatch('notify', ['message' => __('add_projects_to_contact_success'), 'alertType' => 'success']);

                $this->dispatch('toggleAddContactModal', true);

                $this->resetForm();
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->dispatch('notify', ['message' => __('popup.'.$th->getMessage()), 'alertType' => 'error']);

            return;
        }
    }

    public function updatedContactFile()
    {
        $this->importContacts();
    }

    public function importContacts()
    {
        try {
            $validated = $this->validate([
                'contactFile' => ['required', 'file', 'mimes:csv,txt'],
            ]);

            $file = $validated['contactFile'];

            $file_exists = file_exists(storage_path('app/public/livewire-tmp/'.$file->getFilename()));

            if (! $file_exists) {
                throw new \Exception('temporary_file_not_found_error');
            }

            $filename = $file->getFilename();

            $file = fopen(storage_path('app/public/livewire-tmp/'.$file->getFilename()), 'r');

            while (($data = fgetcsv($file, 1000, ';')) !== false) {
                $this->importedContacts[] = [
                    'firstname' => $data[0],
                    'lastname' => $data[1],
                    'email' => $data[2],
                ];
            }

            fclose($file);

            unlink(storage_path('app/public/livewire-tmp/'.$filename));

            $this->showImportedContactsModal = true;

            $this->resetForm();
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->dispatch('notify', ['message' => __('popup.'.$th->getMessage()), 'alertType' => 'error']);

            return;
        }
    }

    public function removeImportedContact($index)
    {
        unset($this->importedContacts[$index]);
    }

    public function createImportedContacts()
    {
        try {
            DB::beginTransaction();

            foreach ($this->importedContacts as $contact) {
                $contact_data = [
                    'firstname' => $contact['firstname'],
                    'lastname' => $contact['lastname'],
                    'email' => $contact['email'],
                    'slug' => Str::slug($contact['firstname'].' '.$contact['lastname']),
                ];

                $user = User::find(auth()->user()->id);

                $contact = $user->contacts()->updateOrCreate(['email' => $contact['email'], 'user_id' => auth()->user()->id], $contact_data);

                $contactImage = ContactImage::updateOrCreate(['contact_id' => $contact->id], [
                    'contact_id' => $contact->id,
                    'image_url' => 'avatars/default-avatar.jpg',
                    'updated_at' => now(),
                ]);

                if (! $contact) {
                    throw new \Exception('add_contact_error');
                }
            }

            DB::commit();

            $this->showImportedContactsModal = false;
            $this->dispatch('toggleAddContactModal');
            $this->importedContacts = [];

            $this->dispatch('notify', ['message' => __('popup.imported_contacts_success'), 'alertType' => 'success']);

            $this->resetForm();
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->dispatch('notify', ['message' => __('popup.'.$th->getMessage()), 'alertType' => 'error']);

            return;
        }
    }

    public function hideImportedContactsModal()
    {
        $this->showImportedContactsModal = false;
        $this->importedContacts = [];
    }

    public function removePicture()
    {
        $this->picture = null;
        $this->dispatch('removePicture');
    }

    public function getUserJiris()
    {
        $user = User::find(auth()->user()->id);

        return $user->jiris()->whereStatus('pending')->get();
    }

    public function updatedJiri()
    {
        if ($this->jiri === '0') {
            $this->jiri = null;

            return;
        }

        $jiri = Jiri::whereId($this->jiri)
            ->whereUserId(auth()->user()->id)
            ->first();

        if (! $jiri) {
            $this->addError('jiri', 'Le jiri sélectionné n’existe pas.');

            return;
        }

        $this->jiriProjects = JiriProject::whereJiriId($jiri->id)->get();

        foreach ($this->jiriProjects as $project) {
            $this->projects[$project->name] = [['link' => '', 'type' => '']];
        }
    }

    public function updatedEmail()
    {
        $this->validate([
            'email' => ['unique:contacts,email,NULL,id,user_id,'.auth()->id()],
        ]);
    }

    public function updatedSwitchRole($switchRole)
    {
        $this->role = $switchRole === true ? 'student' : 'evaluator';
    }

    public function updatedContactProjects()
    {
        // TODO: Add validation for links
        // TODO: Add validation for links type
        // TODO: Add validation for links name
    }

    public function addLinkToProject($name)
    {
        $this->projects[$name][] = ['link' => '', 'type' => ''];
    }

    public function removeLinkFromProject($name, $index)
    {
        unset($this->projects[$name][$index]);
    }

    public function nextStep()
    {
        $this->formStep++;
    }

    public function previousStep()
    {
        $this->formStep--;
    }

    public function resetForm()
    {
        $this->contactId = null;
        $this->formStep = 1;
        $this->switchRole = false;
        $this->role = 'evaluator';
        $this->firstname = '';
        $this->lastname = '';
        $this->email = '';
        $this->picture = null;
        $this->jiri = null;
        $this->projects = [];
        $this->resetErrorBag();
        $this->dispatch('refreshComponent');
    }

    public function render()
    {
        return view('livewire.contacts.add-contact-modal', ['jiris' => $this->getUserJiris()]);
    }
}
