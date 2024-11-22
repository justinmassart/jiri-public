<?php

namespace App\Livewire;

use App\Models\Attendance;
use App\Models\GlobalScore;
use App\Models\Jiri;
use App\Models\ProjectScore;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Home extends Component
{
    protected $layout = 'components.layouts.app';

    public $selectedJiri;

    public bool $showStartedJiris = true;

    public bool $showSelectedJiriAttendances = false;

    public bool $showSelectedJiriScores = false;

    public bool $showAddJiriModal = false;

    public bool $showAddContactModal = false;

    public bool $showSelectedJiriOngoingEvaluations = false;

    public Jiri $selectedPassedJiri;

    public bool $showPassedJiris = false;

    public bool $showSelectedPassedJiriAttendances = false;

    public bool $showSelectedPassedJiriScores = false;

    public bool $showSelectedPassedJiriOngoingEvaluations = false;

    public bool $showEvaluationModal = false;

    public Collection $globalScores;

    public $evaluation;

    public function updateEvaluationStatus(Attendance $studentAttendance, Attendance $evaluatorAttendance, string $status)
    {
        $statusValues = ['to_present', 'ongoing', 'presented'];

        try {
            if (! in_array($status, $statusValues)) {
                throw new \Exception('Le status de l’évaluation doit être une de ces valeurs: '.implode(', ', $statusValues));
            }

            DB::beginTransaction();

            $this->selectedJiri->ongoing_evaluations->where('student_attendance_id', $studentAttendance->id)
                ->where('evaluator_attendance_id', $evaluatorAttendance->id)
                ->first()->update(['status' => $status]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->dispatch('notify', [
                'message' => __('popup.error', ['error' => $th->getMessage()]),
                'alertType' => 'error',
            ]);
        }
    }

    #[Computed]
    public function startedJiris()
    {
        return auth()->user()->jiris()->where('status', 'started')->get();
    }

    #[Computed]
    public function passedJiris()
    {
        return auth()->user()->jiris()->where('status', 'ended')->get();
    }

    #[Computed]
    public function selectedJiriAttendances()
    {
        if (! $this->selectedJiri) {
            return;
        }

        $jiri_projects = $this->selectedJiri->jiri_projects;

        $students = $this->selectedJiri->ongoing_evaluations->map(function ($ongoing_evaluation) use ($jiri_projects) {
            return $ongoing_evaluation->student->load(['projects' => function ($query) use ($jiri_projects) {
                $query->whereIn('jiri_project_id', $jiri_projects->pluck('id'))->whereNotNull('urls')->with('jiri_project');
            }]);
        })->unique('id')->sortBy('lastname');
        $evaluators = $this->selectedJiri->evaluators;

        return [
            'students' => $students,
            'evaluators' => $evaluators,
        ];
    }

    #[Computed]
    public function selectedPassedJiriAttendances()
    {
        if (! $this->selectedPassedJiri) {
            return;
        }

        $students = $this->selectedPassedJiri->students;
        $evaluators = $this->selectedPassedJiri->evaluators;

        return [
            'students' => $students,
            'evaluators' => $evaluators,
        ];
    }

    #[Computed]
    public function selectedJiriScores()
    {
        if (! $this->selectedJiri) {
            return;
        }

        $jiri = $this->selectedJiri;

        $jiri->load([
            'jiri_projects',
            'global_scores.student.attendances' => function ($query) use ($jiri) {
                $query->where('jiri_id', $jiri->id);
            },
            'global_scores.evaluator.attendances' => function ($query) use ($jiri) {
                $query->where('jiri_id', $jiri->id);
            },
            'global_scores.student.projects.jiri_project' => function ($query) use ($jiri) {
                $query->where('jiri_id', $jiri->id);
            },
            'global_scores.student.projects.scores' => function ($query) use ($jiri) {
                $query->where('jiri_id', $jiri->id);
            },
        ]);

        $evaluators = $jiri->global_scores->map(function ($global_score) {
            return $global_score->evaluator;
        })->unique('id');

        $students = $jiri->global_scores->map(function ($global_score) {
            return $global_score->student;
        })->unique('id');

        $selectedJiriProjects = $jiri->jiri_projects;

        $this->dispatch('showScores', true);

        return [
            'evaluators' => $evaluators,
            'students' => $students,
            'selectedJiriProjects' => $selectedJiriProjects,
        ];
    }

    #[Computed]
    public function selectedPassedJiriScores()
    {
        if (! $this->selectedPassedJiri) {
            return;
        }

        $jiri = $this->selectedPassedJiri;

        $jiri->load([
            'jiri_projects',
            'global_scores.student.attendances' => function ($query) use ($jiri) {
                $query->where('jiri_id', $jiri->id);
            },
            'global_scores.evaluator.attendances' => function ($query) use ($jiri) {
                $query->where('jiri_id', $jiri->id);
            },
            'global_scores.student.projects.jiri_project' => function ($query) use ($jiri) {
                $query->where('jiri_id', $jiri->id);
            },
            'global_scores.student.projects.scores' => function ($query) use ($jiri) {
                $query->where('jiri_id', $jiri->id);
            },
        ]);

        $evaluators = $jiri->global_scores->map(function ($global_score) {
            return $global_score->evaluator;
        })->unique('id');

        $students = $jiri->global_scores->map(function ($global_score) {
            return $global_score->student;
        })->unique('id');

        $selectedPassedJiriProjects = $jiri->jiri_projects;

        $this->dispatch('showScores', true);

        return [
            'evaluators' => $evaluators,
            'students' => $students,
            'selectedPassedJiriProjects' => $selectedPassedJiriProjects,
        ];
    }

    #[Computed]
    public function selectedJiriOngoingEvaluations()
    {
        $jiri = $this->selectedJiri;

        $evaluators = $jiri->evaluators->load('attendances')->sortBy('lastname');
        $students = $jiri->ongoing_evaluations->map(function ($ongoing_evaluation) {
            return $ongoing_evaluation->student;
        })->unique('id')->sortBy('lastname');

        return [
            'evaluators' => $evaluators,
            'students' => $students,
            'ongoingEvaluations' => $jiri->ongoing_evaluations,
        ];
    }

    #[Computed]
    public function selectedPassedJiriOngoingEvaluations()
    {
        $jiri = $this->selectedPassedJiri;

        $evaluators = $jiri->evaluators->load('attendances');
        $students = $jiri->students->load('attendances');

        return [
            'evaluators' => $evaluators,
            'students' => $students,
            'ongoingEvaluations' => $jiri->ongoing_evaluations,
        ];
    }

    public function selectJiri(Jiri $jiri)
    {
        if ($this->selectedJiri->id === $jiri->id && $this->showStartedJiris) {
            $this->showStartedJiris = false;

            return;
        }

        $this->selectedJiri = $jiri;
        $this->showStartedJiris = true;
    }

    public function selectPassedJiri(Jiri $jiri)
    {
        $this->selectedPassedJiri = $jiri;

        if ($this->selectedPassedJiri->id === $jiri->id && $this->showPassedJiris) {
            $this->showPassedJiris = false;

            return;
        }

        $this->showPassedJiris = true;
    }

    public function startedJiriTab($tab)
    {
        switch ($tab) {
            case 'attendances':
                $this->showSelectedJiriAttendances = true;
                $this->showSelectedJiriScores = false;
                $this->showSelectedJiriOngoingEvaluations = false;
                break;
            case 'scores':
                $this->showSelectedJiriAttendances = false;
                $this->showSelectedJiriScores = true;
                $this->showSelectedJiriOngoingEvaluations = false;
                break;
            case 'evaluations':
                $this->showSelectedJiriAttendances = false;
                $this->showSelectedJiriScores = false;
                $this->showSelectedJiriOngoingEvaluations = true;
                $this->dispatch('showEvaluations', true);
                break;
        }
    }

    public function passedJiriTab($tab)
    {
        switch ($tab) {
            case 'attendances':
                $this->showSelectedPassedJiriAttendances = true;
                $this->showSelectedPassedJiriScores = false;
                $this->showSelectedPassedJiriOngoingEvaluations = false;
                break;
            case 'scores':
                $this->showSelectedPassedJiriAttendances = false;
                $this->showSelectedPassedJiriScores = true;
                $this->showSelectedPassedJiriOngoingEvaluations = false;
                break;
            case 'evaluations':
                $this->showSelectedPassedJiriAttendances = false;
                $this->showSelectedPassedJiriScores = false;
                $this->showSelectedPassedJiriOngoingEvaluations = true;
                break;
        }
    }

    public function showEvaluation(Attendance $studentAttendance, Attendance $evaluatorAttendance)
    {
        $globalScore = GlobalScore::where('student_attendance_id', $studentAttendance->id)
            ->where('evaluator_attendance_id', $evaluatorAttendance->id)
            ->where('jiri_id', $this->selectedJiri->id)
            ->first();

        $globalScore->load(['student', 'evaluator']);

        $projectsScores = ProjectScore::where('student_attendance_id', $studentAttendance->id)
            ->where('evaluator_attendance_id', $evaluatorAttendance->id)
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
    }

    public function hideEvaluation()
    {
        $this->showEvaluationModal = false;
        $this->evaluation = null;
    }

    public function toggleShowStartedJiris()
    {
        $this->showStartedJiris = ! $this->showStartedJiris;

        if (! $this->showStartedJiris) {
            $this->showSelectedJiriAttendances = false;
            $this->showSelectedJiriScores = false;
            $this->showSelectedJiriOngoingEvaluations = false;
        }
    }

    public function toggleShowPassedJiris()
    {
        $this->showPassedJiris = ! $this->showPassedJiris;

        if (! $this->showPassedJiris) {
            $this->showSelectedPassedJiriAttendances = false;
            $this->showSelectedPassedJiriScores = false;
            $this->showSelectedPassedJiriOngoingEvaluations = false;
        }
    }

    public function welcome()
    {
        $welcome = session()->get('welcome');
        if ($welcome) {
            $message = $welcome === 'user' ? __('popup.welcome') : __('popup.welcome_contact');
            $this->dispatch('notify', [
                'message' => __($message, ['firstname' => auth()->user()->firstname]),
                'alertType' => 'notify',
            ]);
        }
        session()->forget('welcome');
    }

    public function toggleAddJiriModal()
    {
        $this->showAddJiriModal = ! $this->showAddJiriModal;
    }

    public function toggleAddContactModal()
    {
        $this->showAddContactModal = ! $this->showAddContactModal;
    }

    public function mount()
    {
        ! auth()->guard('web')->check()
            ? $this->redirect(LoginOrRegister::class, navigate: true)
            : null;

        $this->selectedJiri = $this->startedJiris()->first() ?? [];

        /* $this->getGlobalScores(); */
    }

    public function render()
    {
        return view('livewire.home')->layout($this->layout);
    }
}
