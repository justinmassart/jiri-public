<?php

namespace App\Livewire\Live;

use App\Livewire\Home;
use App\Models\Attendance;
use App\Models\Jiri;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class LiveEvaluation extends Component
{
    protected $layout = 'components.layouts.live-board';

    public Jiri $jiri;

    public bool $fullscreen = false;

    #[Computed]
    public function jiriOngoingEvaluations()
    {
        $jiri = $this->jiri;

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

    public function updateEvaluationStatus(Attendance $studentAttendance, Attendance $evaluatorAttendance, string $status)
    {
        $statusValues = ['to_present', 'ongoing', 'presented'];

        try {
            if (! in_array($status, $statusValues)) {
                throw new \Exception('Le status de l’évaluation doit être une de ces valeurs: '.implode(', ', $statusValues));
            }

            DB::beginTransaction();

            $this->jiri->ongoing_evaluations->where('student_attendance_id', $studentAttendance->id)
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

    public function toggleFullscreen()
    {
        $this->fullscreen = ! $this->fullscreen;
    }

    public function mount(Jiri $jiri)
    {
        if ($jiri->status !== 'started') {
            $this->redirect(Home::class, navigate: true);
        }

        $this->jiri = $jiri;
    }

    public function render()
    {
        return view('livewire.live.live-evaluation')->layout($this->layout);
    }
}
