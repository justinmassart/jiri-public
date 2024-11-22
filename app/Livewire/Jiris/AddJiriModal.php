<?php

namespace App\Livewire\Jiris;

use App\Models\Contact;
use App\Models\Jiri;
use DateInterval;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

class AddJiriModal extends Component
{
    #[Validate(['required', 'string', 'min:2', 'max:255'])]
    public $name = '';

    #[Validate(['required', 'date', 'after:now'])]
    public $startsAt = '';

    #[Validate(['required'])]
    public $endsAt = '';

    public $selectedStudents = [];

    public $selectedEvaluators = [];

    public $projects = [];

    public $evaluatorSearch = '';

    public $studentSearch = '';

    public $formStep = 1;

    public $jiriId;

    public function createJiri($nextStep = false)
    {
        try {
            DB::beginTransaction();

            $validated = $this->validate();

            $startsAt = new DateTime($validated['startsAt']);
            [$hours, $minutes] = explode(':', $validated['endsAt']);
            $endsAt = clone $startsAt;
            $endsAt->add(new DateInterval("PT{$hours}H{$minutes}M"));

            $jiri = auth()->user()->jiris()->updateOrCreate(
                ['id' => $this->jiriId],
                [
                    'name' => $validated['name'],
                    'starts_at' => $startsAt->format('Y-m-d H:i'),
                    'ends_at' => $endsAt->format('Y-m-d H:i'),
                    'slug' => Str::slug($validated['name'].'-'.$startsAt->format('d-m-y')),
                    'session' => 'june',
                ]
            );

            if (! $jiri) {
                throw new \Exception('add_jiri_error');
            }

            DB::commit();

            $this->jiriId = $jiri->id;

            if ($nextStep) {
                $this->nextStep();
            } else {
                $this->resetForm();
            }

            $this->dispatch('notify', ['message' => __('popup.add_jiri_success', ['name' => $jiri->name]), 'alertType' => 'success']);
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->dispatch('notify', ['message' => __('popup.'.$th->getMessage()), 'alertType' => 'error']);

            return;
        }
    }

    public function addProjectsToJiri($nextStep = false)
    {
        try {
            DB::beginTransaction();

            $jiri = Jiri::find($this->jiriId);

            if (! $jiri) {
                throw new \Exception('jiri_not_found');
            }

            if ($this->projects) {
                foreach ($this->projects as $project) {
                    if (! $project['name']) {

                        continue;
                    }
                    $pj = $jiri->jiri_projects()->updateOrCreate(
                        ['jiri_id' => $jiri->id, 'name' => $project['name']],
                        [
                            'name' => $project['name'],
                            'description' => $project['description'],
                            'slug' => Str::slug($project['name']),
                            'weighting' => $project['weighting'],
                        ]
                    );

                    if (! $pj) {
                        throw new \Exception('add_jiri_projects_error');
                    }
                }
            } else {
                return;
            }

            DB::commit();

            if ($nextStep) {
                $this->nextStep();
            } else {
                $this->resetForm();
            }

            $this->dispatch('notify', ['message' => __('popup.add_projects_to_jiri_success'), 'alertType' => 'success']);
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->dispatch('notify', ['message' => __('popup.'.$th->getMessage()), 'alertType' => 'error']);

            return;
        }
    }

    public function removeProject(int $index)
    {
        unset($this->projects[$index]);
        $this->projects = array_values($this->projects);
    }

    public function addParticipantsToJiri()
    {
        try {
            DB::beginTransaction();

            $jiri = Jiri::find($this->jiriId);

            if (! $jiri) {
                throw new \Exception('jiri_not_found');
            }

            if ($this->selectedEvaluators) {
                foreach ($this->selectedEvaluators as $evaluator) {
                    $jiri->evaluators()->attach($evaluator->id, ['role' => 'evaluator']);
                }
            }

            if ($this->selectedStudents) {
                foreach ($this->selectedStudents as $student) {
                    $jiri->evaluators()->attach($student->id, ['role' => 'student']);
                }
            }

            DB::commit();

            $this->resetForm();

            $this->dispatch('notify', ['message' => __('popup.add_participants_to_jiri_success'), 'alertType' => 'success']);
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->dispatch('notify', ['message' => __('popup.'.$th->getMessage()), 'alertType' => 'error']);

            return;
        }
    }

    #[Computed]
    public function contacts($search)
    {
        if ($search) {
            $query = Contact::whereUserId(auth()->user()->id);

            $query->where(function ($query) use ($search) {
                $query->where('firstname', 'like', '%'.$search.'%')
                    ->orWhere('lastname', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%');
            });

            $query->orderBy('lastname', 'asc');

            return $query->get();
        } else {
            return [];
        }
    }

    #[Computed]
    public function students($studentSearch)
    {
        if ($studentSearch) {
            $query = Contact::whereUserId(auth()->user()->id);

            if (! empty($this->selectedEvaluators)) {
                $evaluatorIds = array_map(function ($evaluator) {
                    return $evaluator->id;
                }, $this->selectedEvaluators);
                $query->whereNotIn('id', $evaluatorIds);
            }

            if (! empty($this->selectedStudents)) {
                $studentIds = array_map(function ($student) {
                    return $student->id;
                }, $this->selectedStudents);
                $query->whereNotIn('id', $studentIds);
            }

            $query->where(function ($query) use ($studentSearch) {
                $query->where('firstname', 'like', '%'.$studentSearch.'%')
                    ->orWhere('lastname', 'like', '%'.$studentSearch.'%')
                    ->orWhere('email', 'like', '%'.$studentSearch.'%');
            });

            $query->orderBy('lastname', 'asc');

            return $query->get();
        } else {
            return [];
        }
    }

    #[Computed]
    public function evaluators($evaluatorSearch)
    {
        if ($evaluatorSearch) {
            $query = Contact::whereUserId(auth()->user()->id);

            if (! empty($this->selectedStudents)) {
                $studentIds = array_map(function ($student) {
                    return $student->id;
                }, $this->selectedStudents);
                $query->whereNotIn('id', $studentIds);
            }

            if (! empty($this->selectedEvaluators)) {
                $evaluatorIds = array_map(function ($evaluator) {
                    return $evaluator->id;
                }, $this->selectedEvaluators);
                $query->whereNotIn('id', $evaluatorIds);
            }

            $query->where(function ($query) use ($evaluatorSearch) {
                $query->where('firstname', 'like', '%'.$evaluatorSearch.'%')
                    ->orWhere('lastname', 'like', '%'.$evaluatorSearch.'%')
                    ->orWhere('email', 'like', '%'.$evaluatorSearch.'%');
            });

            $query->orderBy('lastname', 'asc');

            return $query->get();
        } else {
            return [];
        }
    }

    public function selectEvaluator(Contact $contact)
    {

        if (in_array($contact, $this->selectedEvaluators)) {
            return;
        }

        $this->selectedEvaluators[] = $contact;
    }

    public function removeEvaluator(Contact $contact)
    {
        unset($this->selectedEvaluators[array_search($contact, $this->selectedEvaluators)]);
    }

    public function selectStudent(Contact $contact)
    {
        if (in_array($contact, $this->selectedStudents)) {
            return;
        }

        $this->selectedStudents[] = $contact;
    }

    public function removeStudent(Contact $contact)
    {
        unset($this->selectedStudents[array_search($contact, $this->selectedStudents)]);
    }

    public function addProject()
    {
        $this->projects[] = [
            'name' => '',
            'description' => '',
            'weighting' => 0,
        ];
    }

    public function previousStep()
    {
        $this->formStep--;
    }

    public function nextStep()
    {
        $this->formStep++;
    }

    public function resetForm()
    {
        $this->dispatch('toggleAddJiriModal');
        $this->formStep = 1;
        $this->jiriId = null;
        $this->name = '';
        $this->startsAt = (new DateTime())->setTime(0, 0)->format('Y-m-d H:i');
        $this->endsAt = '00:00';
        $this->selectedStudents = [];
        $this->selectedEvaluators = [];
        $this->projects = [];
        $this->dispatch('refreshComponent');
    }

    public function mount()
    {
        $this->startsAt = (new DateTime())->setTime(0, 0)->format('Y-m-d H:i');
        $this->endsAt = '00:00';
    }

    public function render()
    {
        return view('livewire.jiris.add-jiri-modal');
    }
}
