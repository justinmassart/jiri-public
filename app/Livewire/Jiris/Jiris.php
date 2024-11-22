<?php

namespace App\Livewire\Jiris;

use App\Models\Jiri;
use Illuminate\Http\Request;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Jiris extends Component
{
    use WithPagination;

    protected $layout = 'components.layouts.app';

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

    public $selectedJiri;

    public $showModal = false;

    public $jiriSlug;

    #[Computed]
    public function jiris()
    {
        $query = Jiri::whereUserId(auth()->user()->id)->with('attendances');

        $query->where(function ($query) {
            $query->where('name', 'like', '%'.$this->search.'%')
                ->orWhere('starts_at', 'like', '%'.$this->search.'%')
                ->orWhere('ends_at', 'like', '%'.$this->search.'%');
        });

        $query->orderBy($this->sort ? $this->sort : 'ends_at', $this->order ? $this->order : 'desc');

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

    public function setSelectedJiri($slug)
    {
        $url = '/jiris/'.$slug;

        $this->dispatch('updateUrl', ['url' => $url]);

        $this->selectedJiri = Jiri::whereSlug($slug)->whereUserId(auth()->user()->id)->first();
    }

    #[On('toggleAddJiriModal')]
    public function toggleAddJiriModal()
    {
        $this->showModal = ! $this->showModal;
    }

    public function notify()
    {
        if ($this->jiriSlug && ! $this->selectedJiri) {

            $this->dispatch('notify', [
                'message' => 'jiri_not_found_error',
                'alertType' => 'error',
                'name' => $this->jiriSlug,
            ]);
        }
    }

    #[On('resetSelectedJiri')]
    public function resetSelectedJiri()
    {
        $this->selectedJiri = null;

        $this->showModal = false;

        $queryParams = http_build_query(array_filter([
            'search' => $this->search,
            'sort' => $this->sort,
            'order' => $this->order,
            'page' => $this->page,
        ]));

        $url = '/jiris'.($queryParams ? '?'.$queryParams : '');

        $this->dispatch('updateUrl', ['url' => $url]);
    }

    #[On('refreshComponent')]
    public function refreshComponent()
    {
        $this->resetPage();
    }

    public function mount(Request $request)
    {
        $this->jiriSlug = $request->segment(2);

        if (! $this->jiriSlug) {
            return;
        }

        $this->selectedJiri = Jiri::whereSlug($this->jiriSlug)->whereUserId(auth()->user()->id)->first();

        if (! $this->selectedJiri) {
            return;
        }

        $this->showModal = true;
    }

    public function render()
    {
        return view('livewire.jiris.jiris')->layout($this->layout);
    }
}
