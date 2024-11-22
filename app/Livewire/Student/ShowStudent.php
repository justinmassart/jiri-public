<?php

namespace App\Livewire\Student;

use App\Models\Contact;
use App\Models\Jiri;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ShowStudent extends Component
{
    protected $layout = 'components.layouts.guest';

    public Contact $student;

    public Jiri $jiri;

    public $search;

    public $globalScore;

    public $globalComment;

    public Contact $evaluator;

    public $isEvaluationActive = false;

    public bool $showStartEvaluationModal = false;

    public bool $showStopEvaluationModal = false;

    public $projectsNotScored = [];

    public bool $isGlobalScored = false;

    #[Computed]
    public function otherStudents()
    {
        $query = $this->jiri->students()->where('contacts.id', '!=', $this->student->id)
            ->whereHas('student_ongoing_evaluations');

        $query->where(function ($query) {
            $query->where('firstname', 'like', '%' . $this->search . '%')
                ->orWhere('lastname', 'like', '%' . $this->search . '%');
        });

        $query->orderBy('lastname', 'asc')->with('image');

        return $query->get();
    }

    #[Computed]
    public function canEvaluateStudent()
    {
        $canEvaluate = auth()->user()->evaluator_ongoing_evaluations->where('jiri_id', $this->jiri->id)->where('student_attendance_id', $this->student->attendances->where('jiri_id', $this->jiri->id)->first()->id)->first()->status !== 'to_present' && !auth()->user()->evaluator_ongoing_evaluations
            ->where('jiri_id', $this->jiri->id)
            ->where('student_attendance_id', '!==', $this->student->attendances->where('jiri_id', $this->jiri->id)->first()->id)->contains('status', 'ongoing');

        return $canEvaluate;
    }

    #[Computed]
    public function canStartEvaluation()
    {
        $canStartEvaluation = !auth()->user()->evaluator_ongoing_evaluations
            ->where('jiri_id', $this->jiri->id)
            ->where('student_attendance_id', '!==', $this->student->attendances->where('jiri_id', $this->jiri->id)->first()->id)->contains('status', 'ongoing');

        return $canStartEvaluation;
    }

    public function toggleStartEvaluationModal()
    {
        $this->showStartEvaluationModal = !$this->showStartEvaluationModal;
    }

    public function toggleStopEvaluationModal()
    {
        $this->showStopEvaluationModal = !$this->showStopEvaluationModal;

        if ($this->showStopEvaluationModal) {
            $projects_scores = $this->student->student_projects_scores->where('jiri_id', $this->jiri->id)->where('evaluator_attendance_id', $this->evaluator->attendances->where('jiri_id', $this->jiri->id)->first()->id);
            $global_score = $this->student->student_global_scores->where('jiri_id', $this->jiri->id)->where('evaluator_attendance_id', $this->evaluator->attendances->where('jiri_id', $this->jiri->id)->first()->id)->first();

            $this->isGlobalScored = $global_score->global_score !== null ? true : false;

            $projects_scores->each(function ($project_score) {
                if ($project_score->score === null) {
                    array_push($this->projectsNotScored, $project_score->project->jiri_project);
                }
            });

            return;
        }

        $this->projectsNotScored = [];
    }

    public function startChrono()
    {
        $this->isEvaluationActive = true;

        $this->student->student_ongoing_evaluations()->updateOrCreate(
            [
                'ongoing_evaluations.jiri_id' => $this->jiri->id,
                'student_attendance_id' => $this->student->attendances->where('jiri_id', $this->jiri->id)->first()->id,
                'evaluator_attendance_id' => $this->evaluator->attendances->where('jiri_id', $this->jiri->id)->first()->id,
            ],
            [
                'status' => 'ongoing',
            ]
        );

        $this->student->presentations->where('jiri_id', $this->jiri->id)->where('evaluator_attendance_id', $this->evaluator->attendances->where('jiri_id', $this->jiri->id)->first()->id)->first()->update([
            'started_at' => now(),
        ]);

        session()->put($this->student->slug, 'current');
        session()->put('isChronoActive', true);

        $this->dispatch('startChrono', true);

        $this->showStartEvaluationModal = false;

        $this->dispatch('notify', [
            'message' => __('popup.chrono__started'),
            'alertType' => 'success',
        ]);
    }

    public function stopChrono()
    {
        $this->isEvaluationActive = false;

        $this->student->student_ongoing_evaluations()->updateOrCreate(
            [
                'ongoing_evaluations.jiri_id' => $this->jiri->id,
                'student_attendance_id' => $this->student->attendances->where('jiri_id', $this->jiri->id)->first()->id,
                'evaluator_attendance_id' => $this->evaluator->attendances->where('jiri_id', $this->jiri->id)->first()->id,
            ],
            [
                'status' => 'presented',
            ]
        );

        $this->student->presentations->where('jiri_id', $this->jiri->id)->where('evaluator_attendance_id', $this->evaluator->attendances->where('jiri_id', $this->jiri->id)->first()->id)->first()->update([
            'ended_at' => now(),
        ]);

        session()->put($this->student->slug, 'evaluated');
        session()->put('isChronoActive', false);

        $this->showStopEvaluationModal = false;

        $this->dispatch('stopChrono', false);

        $this->dispatch('notify', [
            'message' => __('popup.chrono__stopped'),
            'alertType' => 'success',
        ]);
    }

    public function updatedGlobalComment()
    {
        $this->student->student_global_scores()->updateOrCreate(
            [
                'global_scores.jiri_id' => $this->jiri->id,
                'student_attendance_id' => $this->student->attendances->where('jiri_id', $this->jiri->id)->first()->id,
                'evaluator_attendance_id' => $this->evaluator->attendances->where('jiri_id', $this->jiri->id)->first()->id,
            ],
            [
                'global_comment' => $this->globalComment ?? null,
            ]
        );
    }

    public function updatedGlobalScore()
    {
        $this->validate(['globalScore' => ['min:0', 'max:20']]);

        $this->student->student_global_scores()->updateOrCreate(
            [
                'global_scores.jiri_id' => $this->jiri->id,
                'student_attendance_id' => $this->student->attendances->where('jiri_id', $this->jiri->id)->first()->id,
                'evaluator_attendance_id' => $this->evaluator->attendances->where('jiri_id', $this->jiri->id)->first()->id,
            ],
            [
                'global_score' => $this->globalScore ?? null,
            ]
        );
    }

    public function showStudent(Contact $student)
    {
        $this->redirectRoute('show.student', ['student' => $student], true, navigate: true);
    }

    public function mount(Jiri $jiri, Contact $student)
    {
        $this->jiri = session()->get('jiri') ?? null;

        if (!$this->jiri || $this->jiri->status !== 'started' || !$this->jiri->students->contains($student) || $student->student_ongoing_evaluations->isEmpty()) {
            $this->redirect(route('evaluator.dashboard', ['jiri' => $this->jiri->slug]));
        }

        $this->evaluator = auth()->user();
        $this->student = $student->load(['image', 'student_ongoing_evaluations' => function ($query) {
            $query->where('evaluator_attendance_id', $this->evaluator->attendances->where('jiri_id', $this->jiri->id)->first()->id);
        }]);
        $studentAttendanceId = $this->student->attendances->where('jiri_id', $this->jiri->id)->first()->id;

        $this->globalScore = $this->evaluator->evaluator_global_scores->where('jiri_id', $this->jiri->id)->where('student_attendance_id', $studentAttendanceId)->first()->global_score ?? null;
        $this->globalComment = $this->evaluator->evaluator_global_scores->where('jiri_id', $this->jiri->id)->where('student_attendance_id', $studentAttendanceId)->first()->global_comment ?? null;
    }

    public function render()
    {
        return view('livewire.student.show-student')->layout($this->layout);
    }
}
