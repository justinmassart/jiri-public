<?php

namespace App\Livewire\Score;

use App\Models\Contact;
use App\Models\GlobalScore;
use App\Models\Jiri;
use App\Models\ProjectScore;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Scores extends Component
{
    use WithPagination;

    protected $layout = 'components.layouts.app';

    public $selectedJiri;

    public $selectedJiriProjects;

    public bool $showModal = false;

    public bool $showEvaluationModal = false;

    public Jiri $jiri;

    public Jiri $jiriSlug;

    public $evaluation;

    public string $search = '';

    public string $sort = '';

    public string $order = '';

    /**
     * @var Collection|Contact[]
     */
    public Collection $evaluators;

    /**
     * @var Collection|Contact[]
     */
    public Collection $students;

    #[Computed]
    public function jiris()
    {
        $query = Jiri::whereUserId(auth()->user()->id)->whereNot('status', 'pending')->with('attendances');

        $query->where(function ($query) {
            $query->where('name', 'like', '%'.$this->search.'%')
                ->orWhere('starts_at', 'like', '%'.$this->search.'%')
                ->orWhere('ends_at', 'like', '%'.$this->search.'%');
        });

        $query->orderBy($this->sort ? $this->sort : 'ends_at', $this->order ? $this->order : 'desc');

        return $query->paginate(12);
    }

    public function setSelectedJiri(Jiri $jiri)
    {
        $tempJiri = $jiri;

        $tempJiri->load([
            'jiri_projects',
            'global_scores.student.attendances' => function ($query) use ($tempJiri) {
                $query->where('jiri_id', $tempJiri->id);
            },
            'global_scores.evaluator.attendances' => function ($query) use ($tempJiri) {
                $query->where('jiri_id', $tempJiri->id);
            },
            'global_scores.student.projects.jiri_project' => function ($query) use ($tempJiri) {
                $query->where('jiri_id', $tempJiri->id);
            },
            'global_scores.student.projects.scores' => function ($query) use ($tempJiri) {
                $query->where('jiri_id', $tempJiri->id);
            },
        ]);

        $this->evaluators = $tempJiri->global_scores->map(function ($global_score) {
            return $global_score->evaluator;
        })->unique('id');

        $this->students = $tempJiri->global_scores->map(function ($global_score) {
            return $global_score->student;
        })->unique('id');

        $this->selectedJiriProjects = $tempJiri->jiri_projects;

        $this->selectedJiri = $tempJiri;

        $this->showModal = true;

        $this->dispatch('showScores', true);
    }

    public function toggleShowEvaluationModal()
    {
        $this->showEvaluationModal = ! $this->showEvaluationModal;
    }

    public function unsetSelectedJiri()
    {
        $this->showEvaluationModal = false;
        $this->showModal = false;

        $this->selectedJiri = null;
        $this->selectedJiriProjects = null;
    }

    public function setSort($sort, $order)
    {
        $this->sort = $sort;
        $this->order = $order;
    }

    public function showEvaluation(Contact $student, Contact $evaluator)
    {
        $studentAttendanceId = $this->selectedJiri->attendances->where('contact_id', $student->id)->first()->id;
        $evaluatorAttendanceId = $this->selectedJiri->attendances->where('contact_id', $evaluator->id)->first()->id;

        $globalScore = GlobalScore::where('student_attendance_id', $studentAttendanceId)
            ->where('evaluator_attendance_id', $evaluatorAttendanceId)
            ->where('jiri_id', $this->selectedJiri->id)
            ->first();

        $globalScore->load(['student', 'evaluator']);

        $projectsScores = ProjectScore::where('student_attendance_id', $studentAttendanceId)
            ->where('evaluator_attendance_id', $evaluatorAttendanceId)
            ->where('jiri_id', $this->selectedJiri->id)
            ->get();

        foreach ($projectsScores as $projectScore) {
            $projectScore->load(['project.jiri_project']);
        }

        $this->evaluation = [
            'global_score' => $globalScore,
            'projects_scores' => $projectsScores,
        ];

        $this->showEvaluationModal = true;

        $this->dispatch('showEvaluation', true);
    }

    public function hideEvaluation()
    {
        $this->showEvaluationModal = false;
        $this->evaluation = null;
    }

    public function mount(Request $request)
    {
        if (! $request->jiri) {
            return;
        }

        $this->setSelectedJiri($request->jiri);
    }

    public function render()
    {
        return view('livewire.score.scores')->layout($this->layout);
    }
}
