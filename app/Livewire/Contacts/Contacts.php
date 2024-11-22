<?php

namespace App\Livewire\Contacts;

use App\Models\Contact;
use Illuminate\Http\Request;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Contacts extends Component
{
    use WithPagination;

    protected $layout = 'components.layouts.app';

    public $title = 'Contacts | Jiri';

    public $page_title = 'Contacts';

    #[Url]
    public $search = '';

    #[Url]
    public $sort = '';

    #[Url]
    public $order = '';

    #[Url]
    public $filter = '';

    #[Url]
    public $page;

    public $selectedContact;

    public $showModal = false;

    public bool $showAddContactModal = false;

    public $contactSlug;

    #[Computed]
    public function contacts()
    {
        $query = Contact::whereUserId(auth()->user()->id)->with('image');

        $query->where(function ($query) {
            $query->where('firstname', 'like', '%'.$this->search.'%')
                ->orWhere('lastname', 'like', '%'.$this->search.'%')
                ->orWhere('email', 'like', '%'.$this->search.'%');
        });

        $query->orderBy($this->sort ? $this->sort : 'lastname', $this->order ? $this->order : 'asc');

        return $query->paginate(12);
    }

    public function setSort($sort, $order)
    {
        $this->sort = $sort;
        $this->order = $order;
    }

    public function updatedPage($page)
    {
        $this->page = $page;
    }

    public function setSelectedContact($contact)
    {
        $url = '/contacts/'.$contact;

        $this->dispatch('updateUrl', ['url' => $url]);

        $this->selectedContact = Contact::whereSlug($contact)->whereUserId(auth()->user()->id)->with('image')->first();
    }

    #[On('resetSelectedContact')]
    public function resetSelectedContact()
    {
        $this->selectedContact = null;

        $queryParams = http_build_query(array_filter([
            'search' => $this->search,
            'sort' => $this->sort,
            'order' => $this->order,
            'page' => $this->page,
        ]));

        $url = '/contacts'.($queryParams ? '?'.$queryParams : '');

        $this->dispatch('updateUrl', ['url' => $url]);
    }

    #[On('toggleAddContactModal')]
    public function toggleAddContactModal()
    {
        $this->showAddContactModal = ! $this->showAddContactModal;
    }

    #[On('contactAdded')]
    public function getNewContacts()
    {
        unset($this->contacts);
    }

    public function notify()
    {
        if ($this->contactSlug && ! $this->selectedContact) {
            $explode = explode('-', $this->contactSlug);
            $firstname = ucfirst($explode[0]);
            $lastname = ucfirst($explode[1]);

            $this->dispatch('notify', [
                'message' => __('contact_not_found_error', [
                    'firstname' => $firstname,
                    'lastname' => $lastname,
                ]),
                'alertType' => 'error',
            ]);
        }
    }

    public function mount(Request $request)
    {
        $this->contactSlug = $request->segment(2);

        if (! $this->contactSlug) {
            return;
        }

        $this->selectedContact = Contact::whereSlug($this->contactSlug)->whereUserId(auth()->user()->id)->first();

        if (! $this->selectedContact) {
            return;
        }

        $this->showModal = true;
    }

    public function render()
    {
        return view('livewire.contacts.contacts')->layout($this->layout);
    }
}
