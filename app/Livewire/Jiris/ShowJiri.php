<?php

namespace App\Livewire\Jiris;

use App\Events\SendLoginTokenToEvaluatorsEvent;
use App\Models\Attendance;
use App\Models\Contact;
use App\Models\GlobalScore;
use App\Models\Jiri;
use App\Models\JiriProject;
use App\Models\OngoingEvaluations;
use App\Models\Presentation;
use App\Models\ProjectScore;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ShowJiri extends Component
{
    use WithFileUploads;

    public $jiri;

    public $hasStarted;

    public $selectedContact;

    public $selectedContactRole;

    public $selectedProject;

    public bool $editContactMode = false;

    public bool $editProjectMode = false;

    public bool $editContactProjectMode = false;

    public bool $showContactEditForm = false;

    public bool $showProjectForm = false;

    public bool $showAddContactsForm = false;

    public bool $showDeleteJiriForm = false;

    public bool $showStartJiriForm = false;

    public bool $switchAddContactType = true;

    public string $addContactType = 'student';

    #[Url]
    public $contactSearch;

    #[Url]
    public $addContactSearch;

    #[Url]
    public $contactSort = '';

    #[Url]
    public $projectSort = '';

    #[Url]
    public $contactOrder = '';

    #[Url]
    public $projectOrder = '';

    #[Validate(['string', 'min:1', 'max:50'])]
    public $newProjectName = '';

    #[Validate(['string', 'min:1', 'max:255'])]
    public $newProjectDescription = '';

    #[Validate(['numeric', 'min:0', 'max:1'])]
    public $newProjectWeighting = '';

    public $jiriProjects = [];

    public $jiriProjectsErrors = [];

    public $jiriFile;

    public $filledJiriFile;

    public bool $showFilledJiriFileModal = false;

    public bool $showStopJiriForm = false;

    public array $studentsWithNoProjects = [];

    public $overOrUnderWeighting;

    public function updatedNewProjectName()
    {
        $this->validate([
            'newProjectName' => ['required', 'string', 'min:1', 'max:50', 'unique:jiri_projects,name,'.$this->jiri->id],
        ]);
    }

    public function updatedSwitchAddContactType()
    {
        if ($this->switchAddContactType === true) {
            $this->addContactType = 'student';
        } else {
            $this->addContactType = 'evaluator';
        }
    }

    #[Computed]
    public function canStartJiri()
    {
        if ($this->jiri->status !== 'pending' || $this->jiri->students->isEmpty() || $this->jiri->evaluators->isEmpty() || $this->jiri->jiri_projects->isEmpty() || $this->jiri->projects->isEmpty()) {
            return false;
        }

        return true;
    }

    #[Computed]
    public function contacts()
    {
        $currentJiriId = $this->jiri->id;

        $query = $this->jiri->contacts()->with(['jiri_projects' => function ($query) use ($currentJiriId) {
            $query->where('jiri_id', $currentJiriId);
        }, 'projects' => function ($query) {
            $query->whereIn('jiri_project_id', $this->jiri->jiri_projects()->pluck('id'));
        }, 'attendances']);

        $query->where(function ($query) {
            $query->where('firstname', 'like', '%'.$this->contactSearch.'%')
                ->orWhere('lastname', 'like', '%'.$this->contactSearch.'%')
                ->orWhere('email', 'like', '%'.$this->contactSearch.'%');
        });

        $query->orderBy($this->contactSort ? $this->contactSort : 'lastname', $this->contactOrder ? $this->contactOrder : 'asc');

        return $query->get();
    }

    #[Computed]
    public function add_contact_list()
    {
        if ($this->addContactSearch) {

            $query = Contact::whereUserId(auth()->user()->id);

            $jiriContactIds = $this->contacts()->pluck('id');

            $query->where(function ($query) {
                $query->where('firstname', 'like', '%'.$this->addContactSearch.'%')
                    ->orWhere('lastname', 'like', '%'.$this->addContactSearch.'%')
                    ->orWhere('email', 'like', '%'.$this->addContactSearch.'%');
            })
                ->whereNotIn('id', $jiriContactIds);

            $query->orderBy('lastname', 'asc');

            return $query->get();
        } else {
            return [];
        }
    }

    #[Computed]
    public function projects()
    {
        if (! $this->jiri->id) {
            return;
        }

        $query = $this->jiri->projects();

        $query->orderBy($this->projectSort ? $this->projectSort : 'name', $this->projectOrder ? $this->projectOrder : 'asc');

        return $query->get();
    }

    public function updateLink($projectName, $index, $value)
    {
        if (! filter_var($value, FILTER_VALIDATE_URL)) {
            $this->jiriProjectsErrors[$projectName][$index] = __('title.invalid_url');

            return;
        }

        if (isset($this->jiriProjectsErrors[$projectName][$index])) {
            unset($this->jiriProjectsErrors[$projectName][$index]);
        }

        $projects = json_decode($this->jiriProjects[$projectName], true);
        $projects[$index]['link'] = $value;
        $this->jiriProjects[$projectName] = json_encode($projects);
    }

    public function updateType($projectName, $index, $value)
    {
        $projects = json_decode($this->jiriProjects[$projectName], true);
        $projects[$index]['type'] = $value;
        $this->jiriProjects[$projectName] = json_encode($projects);
    }

    public function studentProjects()
    {
        if (! $this->selectedContact) {
            return;
        }

        $jiri_projects = $this->jiri->jiri_projects;

        $this->jiriProjects = $jiri_projects->reduce(function ($carry, $jiriProject) {
            $projects = $this->selectedContact->projects->whereIn('jiri_project_id', $jiriProject->id);
            foreach ($projects as $project) {
                $carry[$jiriProject->name] = $project->urls ?? '[]';
            }

            if (! array_key_exists($jiriProject->name, $carry)) {
                $carry[$jiriProject->name] = json_encode([['link' => '', 'type' => '']]);
            }

            return $carry;
        }, []);

        if ($this->jiriProjects === []) {
            $this->jiriProjects = $jiri_projects->reduce(function ($carry, $jiriProject) {
                $carry[$jiriProject->name] = json_encode([['link' => '', 'type' => '']]);

                return $carry;
            }, []);
        }
    }

    public function addNewContactToJiri(Contact $contact)
    {
        if (auth()->user()->id !== $this->jiri->user_id && auth()->user()->id !== $contact->user_id) {
            $this->dispatch('notify', ['message' => 'action_unauthorized', 'alertType' => 'error']);

            return;
        }

        $this->jiri->contacts()->attach($contact->id, [
            'role' => $this->addContactType,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function updateOrCreateProject(JiriProject $project = null)
    {
        $validated = $this->validate([
            'newProjectName' => 'required|string|min:1|max:50',
            'newProjectDescription' => 'required|string|min:1|max:255',
            'newProjectWeighting' => 'required|numeric|min:0|max:1',
        ]);

        try {
            DB::beginTransaction();

            $newProject = $this->jiri->projects()->updateOrCreate([
                'id' => $project ? $project->id : null,
            ], [
                'name' => $validated['newProjectName'],
                'description' => $validated['newProjectDescription'],
                'slug' => Str::slug($validated['newProjectName'].'-'.$this->jiri->id),
                'weighting' => $validated['newProjectWeighting'],
            ]);

            if (! $newProject) {
                throw new \Exception('add_project_error');
            }

            DB::commit();

            $this->dispatch('notify', ['message' => 'add_project_success', 'alertType' => 'success', 'name' => $validated['newProjectName']]);

            $this->showProjectForm = false;

            $this->reset('newProjectName', 'newProjectDescription', 'newProjectWeighting', 'selectedProject');
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->dispatch('notify', ['message' => __('popup.'.$th->getMessage()), 'alertType' => 'error']);

            return;
        }
    }

    public function saveJiriProjects()
    {
        try {
            DB::beginTransaction();

            $failures = [];

            $jiriProjects = array_filter($this->jiriProjects, function ($links) {
                $linksArray = json_decode($links, true);
                if (is_array($linksArray) && count($linksArray) > 0) {
                    foreach ($linksArray as $link) {
                        if (! empty($link['link'])) {
                            return true;
                        }
                    }
                }

                return false;
            });

            foreach ($jiriProjects as $name => $links) {
                $jiriProjectId = JiriProject::whereName($name)->whereJiriId($this->jiri->id)->first()->id;
                $project = $this->selectedContact->projects()->updateOrCreate([
                    'jiri_project_id' => $jiriProjectId,
                    'contact_id' => $this->selectedContact->id,
                ], [
                    'urls' => $jiriProjects[$name],
                ]);
                if (! $project) {
                    $failures[] = $name;
                }
            }

            if (count($failures) > 0) {
                $message = 'La mise à jour des projets a échoué pour les projets suivants : '.implode(', ', $failures);
                throw new \Exception($message);
            }

            DB::commit();

            $this->dispatch('notify', ['message' => 'add_link_to_project_success', 'alertType' => 'success', 'name' => $this->newProjectName]);

            $this->showContactEditForm = false;

            $this->reset('newProjectName', 'newProjectDescription', 'newProjectWeighting', 'selectedProject');
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->dispatch('notify', ['message' => __('popup.'.$th->getMessage()), 'alertType' => 'error']);

            return;
        }
    }

    public function addLinkToProject($name)
    {
        $projects = json_decode($this->jiriProjects[$name], true);

        $projects[] = ['link' => '', 'type' => ''];

        $this->jiriProjects[$name] = json_encode($projects);
    }

    public function removeLinkFromProject($name, $index)
    {
        $projects = json_decode($this->jiriProjects[$name], true);

        if ($projects && array_key_exists($index, $projects)) {
            if (array_key_exists($name, $this->jiriProjectsErrors)) {
                unset($this->jiriProjectsErrors[$name][$index]);
            }
            unset($projects[$index]);

            $this->jiriProjects[$name] = json_encode($projects);
        }
    }

    public function removeContactFromJiri(Contact $contact)
    {
        if (! $this->jiri->contacts->contains($contact) && auth()->user()->id !== $this->jiri->user_id && auth()->user()->id !== $contact->user_id) {
            $this->dispatch('notify', ['message' => 'action_unauthorized', 'alertType' => 'error']);

            return;
        }
        $this->jiri->contacts()->detach($contact->id);
    }

    public function removeProjectFormJiri(JiriProject $project)
    {
        if ($project->jiri_id !== $this->jiri->id || auth()->user()->id !== $this->jiri->user_id) {
            $this->dispatch('notify', ['message' => 'action_unauthorized', 'alertType' => 'error']);

            return;
        }
        $project->delete();
        $this->dispatch('notify', ['message' => 'delete_project_success', 'alertType' => 'success']);
    }

    public function startJiri()
    {
        if (! $this->jiri || auth()->user()->id !== $this->jiri->user_id) {
            $this->dispatch('notify', ['message' => 'action_unauthorized', 'alertType' => 'error']);

            return;
        }

        try {
            DB::beginTransaction();

            $this->jiri->status = 'started';
            $this->jiri->save();

            $this->hasStarted = true;

            $students = $this->jiri->contacts()->wherePivot('role', 'student')->get();
            $evaluators = $this->jiri->contacts()->wherePivot('role', 'evaluator')->get();
            $jiri_projects = $this->jiri->jiri_projects()->get();

            foreach ($students as $student) {
                $studentAttendance = Attendance::whereContactId($student->id)->whereJiriId($this->jiri->id)->first();
                foreach ($jiri_projects as $jiri_project) {
                    $projectExists = $student->projects->contains('jiri_project_id', $jiri_project->id);
                    if (! $projectExists) {
                        $student->projects()->create([
                            'urls' => null,
                            'jiri_project_id' => $jiri_project->id,
                            'contact_id' => $student->id,
                        ]);
                        $student->refresh();
                    }
                }
                foreach ($evaluators as $evaluator) {
                    $evaluatorAttendance = Attendance::whereContactId($evaluator->id)->whereJiriId($this->jiri->id)->first();
                    $data = [
                        'student_attendance_id' => $studentAttendance->id,
                        'evaluator_attendance_id' => $evaluatorAttendance->id,
                        'jiri_id' => $this->jiri->id,
                    ];
                    $allUrlsNull = true;
                    foreach ($student->projects as $project) {
                        if ($project->urls === null) {
                            ProjectScore::create([
                                'score' => 0,
                                'comment' => null,
                                'is_public' => true,
                                'project_id' => $project->id,
                                'student_attendance_id' => $studentAttendance->id,
                                'evaluator_attendance_id' => $evaluatorAttendance->id,
                                'jiri_id' => $this->jiri->id,
                            ]);
                        } else {
                            $allUrlsNull = false;
                            ProjectScore::create([
                                'project_id' => $project->id,
                                'student_attendance_id' => $studentAttendance->id,
                                'evaluator_attendance_id' => $evaluatorAttendance->id,
                                'jiri_id' => $this->jiri->id,
                            ]);
                        }
                    }
                    if ($allUrlsNull) {
                        GlobalScore::create([
                            'global_score' => 0,
                            'global_comment' => null,
                            'is_public' => true,
                            'student_attendance_id' => $studentAttendance->id,
                            'evaluator_attendance_id' => $evaluatorAttendance->id,
                            'jiri_id' => $this->jiri->id,
                        ]);
                    } else {
                        GlobalScore::create($data);
                        Presentation::create($data);
                        OngoingEvaluations::create($data);
                    }
                }
            }

            foreach ($this->jiri->contacts as $contact) {
                if ($contact->attendances()->whereJiriId($this->jiri->id)->first()->role === 'evaluator') {
                    $token = $contact->token()->create([
                        'token' => Str::random(32),
                        'expires_at' => $this->jiri->ends_at,
                        'jiri_id' => $this->jiri->id,
                    ]);
                    SendLoginTokenToEvaluatorsEvent::dispatch($token);
                }
            }

            DB::commit();

            $this->showStartJiriForm = false;

            $this->dispatch('notify', ['message' => __('start_jiri_success', ['name' => $this->jiri->name]), 'alertType' => 'success']);
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->dispatch('notify', ['message' => __('popup.'.$th->getMessage()), 'alertType' => 'error']);

            return;
        }
    }

    public function exportJiri()
    {
        $students = $this->jiri->contacts()->wherePivot('role', 'student')->get();
        $jiri_projects = $this->jiri->jiri_projects()->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Prénom ↓');
        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->setCellValue('B1', 'Nom ↓');
        $sheet->getColumnDimension('B')->setWidth(10);
        $sheet->setCellValue('C1', 'Projets →');
        $sheet->getColumnDimension('C')->setWidth(10);

        $column = 'D';

        $colors = [];
        for ($i = 0; $i < count($jiri_projects); $i++) {
            $colors[] = sprintf('%02x%02x%02x', rand(200, 255), rand(200, 255), rand(200, 255));
        }
        $colorIndex = 0;

        foreach ($jiri_projects as $project) {
            $color = $colors[$colorIndex++ % count($colors)];

            $startColumn = $column;
            $sheet->setCellValue($column.'1', $project->name);
            $endColumn = chr(ord($column) + 2);
            $sheet->mergeCells($startColumn.'1:'.$endColumn.'1');
            $sheet->getStyle($startColumn.'1:'.$endColumn.'1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue($column.'2', 'Github');
            $sheet->getColumnDimension($column)->setWidth(40);
            $sheet->getStyle($column.'2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $column++;
            $sheet->setCellValue($column.'2', 'Design');
            $sheet->getColumnDimension($column)->setWidth(40);
            $sheet->getStyle($column.'2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $column++;
            $sheet->setCellValue($column.'2', 'Site');
            $sheet->getColumnDimension($column)->setWidth(40);
            $sheet->getStyle($column.'2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $column++;

            $sheet->getStyle($startColumn.'1:'.$endColumn.(count($students) + 2))->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB($color);

            $sheet->getStyle($startColumn.'1:'.$endColumn.(count($students) + 2))->getBorders()
                ->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            $column = chr(ord($column) + 1);
        }

        $row = 3;
        foreach ($students as $student) {
            $sheet->setCellValue('A'.$row, $student->firstname);
            $sheet->setCellValue('B'.$row, $student->lastname);

            $column = 'D';
            foreach ($jiri_projects as $jiri_project) {
                $student_project = $student->projects->where('jiri_project_id', $jiri_project->id)->first();
                if ($student_project && $student_project->urls) {
                    $urls = json_decode($student_project->urls, true);
                    if ($urls) {
                        foreach ($urls as $url) {
                            if ($url['type'] == 'github') {
                                $sheet->setCellValue($column.$row, $url['link']);
                            } elseif ($url['type'] == 'design') {
                                $sheet->setCellValue(chr(ord($column) + 1).$row, $url['link']);
                            } elseif ($url['type'] == 'site') {
                                $sheet->setCellValue(chr(ord($column) + 2).$row, $url['link']);
                            }
                        }
                    }
                }
                $column = chr(ord($column) + 4);
            }
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($this->jiri->slug.'.xlsx');

        return response()->download($this->jiri->slug.'.xlsx')->deleteFileAfterSend(true);
    }

    public function updatedJiriFile()
    {
        $this->validate([
            'jiriFile' => 'required|mimes:xlsx',
        ]);

        $this->importJiri();
    }

    public function importJiri()
    {
        $filePath = $this->jiriFile->getRealPath();
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();

        $rowIndex = 3;

        while ($worksheet->getCell('A'.$rowIndex)->getValue() !== null) {
            $student = [
                'firstname' => $worksheet->getCell('A'.$rowIndex)->getValue(),
                'lastname' => $worksheet->getCell('B'.$rowIndex)->getValue(),
                'projects' => [],
            ];

            $columnIndex = 'D';
            while ($worksheet->getCell($columnIndex.'1')->getValue() !== null) {
                $github = $worksheet->getCell($columnIndex.$rowIndex)->getValue();
                $design = $worksheet->getCell(chr(ord($columnIndex) + 1).$rowIndex)->getValue();
                $site = $worksheet->getCell(chr(ord($columnIndex) + 2).$rowIndex)->getValue();

                $github = $github instanceof \PhpOffice\PhpSpreadsheet\RichText\RichText ? $github->getPlainText() : $github;
                $design = $design instanceof \PhpOffice\PhpSpreadsheet\RichText\RichText ? $design->getPlainText() : $design;
                $site = $site instanceof \PhpOffice\PhpSpreadsheet\RichText\RichText ? $site->getPlainText() : $site;

                $project = [
                    'name' => $worksheet->getCell($columnIndex.'1')->getValue(),
                    'github' => $github,
                    'design' => $design,
                    'site' => $site,
                ];
                $student['projects'][] = $project;
                $columnIndex = chr(ord($columnIndex) + 4);
            }

            $this->filledJiriFile[] = $student;
            $rowIndex++;
        }

        $this->showFilledJiriFileModal = true;

        $this->jiriFile = null;

        unlink($filePath);
    }

    public function updateStudentsProjects()
    {
        try {
            DB::beginTransaction();

            foreach ($this->filledJiriFile as $contact) {
                $student = $this->jiri->students->where('firstname', $contact['firstname'])->where('lastname', $contact['lastname'])->first();
                $jiri_projects = $this->jiri->jiri_projects;

                foreach ($contact['projects'] as $project) {
                    $jiri_project = $jiri_projects->where('name', $project['name'])->first();

                    $data = [];
                    if (isset($project['github'])) {
                        $data[] = ['link' => $project['github'], 'type' => 'github'];
                    }
                    if (isset($project['design'])) {
                        $data[] = ['link' => $project['design'], 'type' => 'design'];
                    }
                    if (isset($project['wordpress'])) {
                        $data[] = ['link' => $project['wordpress'], 'type' => 'wordpress'];
                    }
                    if (isset($project['divers'])) {
                        $data[] = ['link' => $project['divers'], 'type' => 'divers'];
                    }

                    if ($data === []) {
                        continue;
                    }

                    $student->projects()->updateOrCreate([
                        'jiri_project_id' => $jiri_project->id,
                        'contact_id' => $student->id,
                    ], [
                        'urls' => json_encode($data),
                    ]);
                }
            }

            DB::commit();

            $this->showFilledJiriFileModal = false;

            $this->dispatch('notify', ['message' => __('popup.students_projects_updated'), 'alertType' => 'success']);
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->dispatch('notify', ['message' => __('popup.'.$th->getMessage()), 'alertType' => 'error']);

            return;
        }
    }

    public function stopJiri(Jiri $jiri)
    {
        if (! $jiri || auth()->user()->id !== $jiri->user_id) {
            $this->dispatch('notify', ['message' => 'action_unauthorized', 'alertType' => 'error']);

            return;
        }

        try {
            DB::beginTransaction();

            $jiri->status = 'ended';
            $jiri->save();
            $this->hasStarted = false;

            $this->jiri->access_tokens()->delete();

            foreach ($this->jiri->projects_scores as $project_score) {
                $project_score->is_public = true;
                $project_score->save();
            }

            foreach ($this->jiri->global_scores as $global_score) {
                $global_score->is_public = true;
                $global_score->save();
            }

            DB::commit();

            $this->showStopJiriForm = false;

            $this->dispatch('notify', ['message' => __('stop_jiri_success', ['name' => $jiri->name]), 'alertType' => 'success']);
        } catch (\Throwable $th) {
            DB::rollBack();

            $this->dispatch('notify', ['message' => __('popup.'.$th->getMessage()), 'alertType' => 'error']);
        }
    }

    public function deleteJiri()
    {
        if (auth()->user()->id !== $this->jiri->user_id || ! $this->jiri || ! auth()->user()->jiris->contains($this->jiri)) {
            $this->dispatch('notify', ['message' => 'action_unauthorized', 'alertType' => 'error']);

            return;
        }
        $this->jiri->delete();
        $this->dispatch('notify', ['message' => 'delete_jiri_success', 'alertType' => 'success', 'name' => $this->jiri->name]);
        $this->jiri = null;
        $this->redirect(Jiris::class, navigate: true);
        $this->dispatch('refreshComponent');
        $this->dispatch('resetSelectedJiri');
    }

    public function toggleShowFilledJiriModal()
    {
        $this->showFilledJiriFileModal = ! $this->showFilledJiriFileModal;

        if (! $this->showFilledJiriFileModal) {
            $this->filledJiriFile = [];
        }
    }

    public function toggleStopJiriModal()
    {
        $this->showStopJiriForm = ! $this->showStopJiriForm;
    }

    public function toggleEditContactProjectForm(Contact $contact = null)
    {
        $this->showContactEditForm = ! $this->showContactEditForm;
        if (! $this->showContactEditForm) {
            $this->selectedContact = null;
            $this->selectedContactRole = null;
            $this->jiriProjects = [];

            return;
        }
        $this->selectedContact = $contact;
        $this->selectedContactRole = $contact->attendances()->whereJiriId($this->jiri->id)->first()->role;
        $this->jiriProjects = $contact->projects->mapWithKeys(function ($project) {
            if (! $project || ! $project->jiri_projects || ! $project->jiri_projects->name) {
                return [];
            }

            return [$project->jiri_projects->name => json_decode($project->urls) ?? []];
        })->toArray();

        $this->studentProjects();
    }

    public function toggleShowProjectForm(JiriProject $project = null)
    {
        $this->showProjectForm = ! $this->showProjectForm;
        if (! $this->showProjectForm) {
            $this->reset('newProjectName', 'newProjectDescription', 'newProjectWeighting', 'selectedProject');
        } else {
            $this->selectedProject = $project->name ? $project : null;
            $this->newProjectName = $project ? $project->name : '';
            $this->newProjectDescription = $project ? $project->description : '';
            $this->newProjectWeighting = $project ? $project->weighting : '';
        }
    }

    public function toggleEditContactProject()
    {
        $this->showContactEditForm = ! $this->showContactEditForm;
    }

    public function toggleEditContactMode()
    {
        $this->editContactMode = ! $this->editContactMode;
    }

    public function toggleAddContactsForm()
    {
        $this->showAddContactsForm = ! $this->showAddContactsForm;

        if (! $this->showAddContactsForm) {
            $this->addContactSearch = null;
        }
    }

    public function toggleEditProjectMode()
    {
        $this->editProjectMode = ! $this->editProjectMode;
    }

    public function toggleDeleteJiriModal()
    {
        $this->showDeleteJiriForm = ! $this->showDeleteJiriForm;
    }

    public function toggleStartJiriModal()
    {
        $this->showStartJiriForm = ! $this->showStartJiriForm;

        if ($this->showStartJiriForm) {
            $jiri_projects = $this->jiri->jiri_projects;
            $totalWeighting = $jiri_projects->sum('weighting');
            $students = $this->jiri->contacts()->wherePivot('role', 'student')->get();

            if ($totalWeighting !== 1) {
                $this->overOrUnderWeighting = $totalWeighting;
            }

            foreach ($jiri_projects as $jiri_project) {
                foreach ($students as $student) {
                    $project = $student->projects->where('jiri_project_id', $jiri_project->id)->first();

                    if (! $project || $project->urls === null) {
                        $this->studentsWithNoProjects[$jiri_project->name][] = $student;
                    }
                }
            }
        } else {
            $this->studentsWithNoProjects = [];
            $this->overOrUnderWeighting = null;
        }
    }

    public function setContactSort($sort, $order)
    {
        $this->contactSort = $sort;
        $this->contactOrder = $order;
    }

    public function setProjectSort($sort, $order)
    {
        $this->projectSort = $sort;
        $this->projectOrder = $order;
    }

    public function resetSelectedJiri()
    {
        $this->dispatch('refreshComponent');
        $this->dispatch('resetSelectedJiri');
    }

    public function mount(Jiri $jiri)
    {
        if (! $jiri) {
            return;
        }
        $this->jiri = $jiri;
        $this->hasStarted = $jiri->status === 'started' ? true : false;
    }

    public function render()
    {
        return view('livewire.jiris.show-jiri');
    }
}
