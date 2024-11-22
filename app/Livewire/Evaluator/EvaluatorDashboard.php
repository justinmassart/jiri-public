<?php

namespace App\Livewire\Evaluator;

use App\Livewire\LoginOrRegister;
use App\Models\Contact;
use App\Models\Presentation;
use Auth;
use Illuminate\Http\Request;
use Livewire\Attributes\Computed;
use Livewire\Component;

class EvaluatorDashboard extends Component
{
    protected $layout = 'components.layouts.guest';

    public $selectedJiri;

    public $showFinishModal = false;

    public $studentsWithNoGlobalScore = [];

    public $projectsWithNoScore = [];

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

    public function showStudent(Contact $student)
    {
        $this->redirectRoute('show.student', ['student' => $student], true, navigate: true);
    }

    #[Computed]
    public function presentations()
    {
        if (auth()->user() && auth()->user()->getTable() === 'contacts') {
            $evualtorAttendance = auth()->user()->attendances()->where('contact_id', auth()->user()->id)->where('jiri_id', $this->selectedJiri->id)->first();

            $presentations = Presentation::where('evaluator_attendance_id', $evualtorAttendance->id)->get();

            $presentations->load(['student.projects.jiri_project', 'student.image']);

            $presentationsLeft = $presentations->where('ended_at', null)->isEmpty() ? [] : $presentations->where('ended_at', null);
            $presentationsPassed = $presentations->whereNotNull('ended_at')->isEmpty() ? [] : $presentations->where('ended_at', ! null);

            return [
                'left' => $presentationsLeft,
                'passed' => $presentationsPassed,
            ];
        }

        return [];
    }

    public function toggleFinishModal()
    {
        $this->showFinishModal = ! $this->showFinishModal;

        if ($this->showFinishModal) {
            $global_scores = auth()->user()->evaluator_global_scores->where('jiri_id', $this->selectedJiri->id);
            $projects_scores = auth()->user()->evaluator_projects_scores->where('jiri_id', $this->selectedJiri->id);

            foreach ($global_scores as $global_score) {
                if (! isset($global_score->global_score)) {
                    $this->studentsWithNoGlobalScore[] = $global_score->student;
                }
            }

            foreach ($projects_scores as $project_score) {
                if ($project_score->score === null) {
                    $this->projectsWithNoScore[$project_score->student->slug][] = $project_score->load(['student', 'project.jiri_project']);
                }
            }

            return;
        }

        $this->studentsWithNoGlobalScore = [];
        $this->projectsWithNoScore = [];
    }

    public function terminateEvaluation()
    {
        $evaluator = auth()->user();
        $evaluatorScores = $evaluator->evaluator_global_scores->where('jiri_id', $this->selectedJiri->id);
        $evaluatorProjectsScores = $evaluator->evaluator_projects_scores->where('jiri_id', $this->selectedJiri->id);
        $evaluator->access_tokens->where('jiri_id', $this->selectedJiri->id)->first()->delete();

        foreach ($evaluatorScores as $score) {
            $score->is_public = true;
            $score->save();
        }

        foreach ($evaluatorProjectsScores as $score) {
            $score->is_public = true;
            $score->save();
        }

        Auth::guard('contact')->logout();
        //auth()->logout();

        session()->forget('jiri');
        session()->forget('welcome');

        return $this->redirect(LoginOrRegister::class, navigate: true);
    }

    public function mount(Request $request)
    {
        ! auth()->check()
            ? $this->redirect(LoginOrRegister::class, navigate: true)
            : null;

        $jiri = $request->jiri ?? null;
        $isCorrectJiri = session()->get('jiri')->slug === $jiri;

        if (! $isCorrectJiri) {
            return $this->redirect(route('evaluator.dashboard', ['jiri' => session()->get('jiri')->slug]), navigate: true);
        }

        $this->selectedJiri = auth()->user()->jiris()->whereJiriId(session()->get('jiri')->id)->first();
    }

    public function render()
    {
        return view('livewire.evaluator.evaluator-dashboard')->layout($this->layout);
    }
}
