<?php

namespace App\Livewire\Student;

use App\Models\Contact;
use App\Models\Jiri;
use App\Models\JiriProject;
use App\Models\Project;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ShowProject extends Component
{
    protected $layout = 'components.layouts.guest';

    public JiriProject $jiri_project;

    public Contact $student;

    public Contact $evaluator;

    public Jiri $jiri;

    public Project $project;

    #[Validate(['string', 'min:0', 'max:750'])]
    public string $comment;

    public float $score;

    public string $search = '';

    #[Computed]
    public function otherStudents()
    {
        $query = $this->jiri->students()->where('contacts.id', '!=', $this->student->id)
            ->whereHas('student_ongoing_evaluations');

        $query->where(function ($query) {
            $query->where('firstname', 'like', '%'.$this->search.'%')
                ->orWhere('lastname', 'like', '%'.$this->search.'%');
        });

        $query->orderBy('lastname', 'asc')->with('image');

        return $query->get();
    }

    #[Computed]
    public function canEvaluateStudent()
    {
        $canEvaluate = auth()->user()->evaluator_ongoing_evaluations->where('jiri_id', $this->jiri->id)->where('student_attendance_id', $this->student->attendances->where('jiri_id', $this->jiri->id)->first()->id)->first()->status !== 'to_present' && ! auth()->user()->evaluator_ongoing_evaluations
            ->where('jiri_id', $this->jiri->id)
            ->where('student_attendance_id', '!==', $this->student->attendances->where('jiri_id', $this->jiri->id)->first()->id)->contains('status', 'ongoing');

        return $canEvaluate;
    }

    public function updatedScore()
    {
        $this->validate(['score' => ['min:0', 'max:20']]);

        $project_score = $this->project->scores()->updateOrCreate(
            [
                'student_attendance_id' => $this->student->attendances->where('jiri_id', session()->get('jiri')->id)->first()->id,
                'evaluator_attendance_id' => auth()->user()->attendances->where('jiri_id', session()->get('jiri')->id)->first()->id,
                'project_id' => $this->project->id,
                'jiri_id' => session()->get('jiri')->id,
            ],
            [
                'score' => $this->score,
                'student_attendance_id' => $this->student->attendances->where('jiri_id', session()->get('jiri')->id)->first()->id,
                'evaluator_attendance_id' => auth()->user()->attendances->where('jiri_id', session()->get('jiri')->id)->first()->id,
                'jiri_id' => session()->get('jiri')->id,
            ]
        );
    }

    public function updatedComment()
    {
        $this->validate(['comment' => ['string', 'min:0', 'max:750']]);

        $project_score = $this->project->scores()->updateOrCreate(
            [
                'student_attendance_id' => $this->student->attendances->where('jiri_id', session()->get('jiri')->id)->first()->id,
                'evaluator_attendance_id' => auth()->user()->attendances->where('jiri_id', session()->get('jiri')->id)->first()->id,
                'project_id' => $this->project->id,
                'jiri_id' => session()->get('jiri')->id,
            ],
            [
                'comment' => $this->comment,
                'student_attendance_id' => $this->student->attendances->where('jiri_id', session()->get('jiri')->id)->first()->id,
                'evaluator_attendance_id' => auth()->user()->attendances->where('jiri_id', session()->get('jiri')->id)->first()->id,
                'jiri_id' => session()->get('jiri')->id,
            ]
        );
    }

    public function mount(Jiri $jiri, Contact $student, JiriProject $jiri_project)
    {
        if (! $student || auth()->user()->attendances->contains($student->id) || ! $student->jiri_projects->contains($jiri_project) || ! $jiri_project || $jiri_project->projects->where('contact_id', $student->id)->whereNotNull('urls')->isEmpty()) {
            return $this->redirect(route('show.student', ['jiri' => $jiri->slug, 'student' => $student->slug]));
        }

        $this->jiri_project = $jiri_project ?? null;
        $this->jiri = $jiri_project->jiri ?? null;
        $this->evaluator = auth()->user() ?? null;
        $this->student = $student->load(['image', 'student_ongoing_evaluations' => function ($query) {
            $query->where('evaluator_attendance_id', $this->evaluator->attendances->where('jiri_id', $this->jiri->id)->first()->id);
        }]) ?? null;
        $this->project = $jiri_project->projects->where('contact_id', $student->id)->first() ?? null;

        $studentAttendanceId = $this->student->attendances->where('jiri_id', $this->jiri_project->jiri->id)->first()->id;
        $evaluatorAttendanceId = $this->evaluator->attendances->where('jiri_id', $this->jiri_project->jiri->id)->first()->id;

        $this->score = $this->project->scores->where('student_attendance_id', $studentAttendanceId)->where('evaluator_attendance_id', $evaluatorAttendanceId)->first()->score ?? 0;
        $this->comment = $this->project->comments->where('student_attendance_id', $studentAttendanceId)->where('evaluator_attendance_id', $evaluatorAttendanceId)->first()->comment ?? '';
    }

    public function render()
    {
        return view('livewire.student.show-project')->layout($this->layout);
    }
}
