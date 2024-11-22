<div class="home" wire:init="welcome">
    <x-slot:title>
        {!! __('title.home') . ' | Jiri' !!}
    </x-slot:title>
    <x-slot:page_title>
        {!! __('title.home') !!}
    </x-slot:page_title>
    @if (!$this->startedJiris->isEmpty())
        <div class="home__startedJiris card relative">
            <div wire:click.prevent="toggleShowStartedJiris"
                class="home__startedJiris__arrow arrow {!! $showStartedJiris ? 'arrow-up' : 'arrow-down' !!}">&#8595;</div>
            <ul class="home__startedJiris__list rowList">
                @foreach ($this->startedJiris as $jiri)
                    <li class="home__startedJiris__list__item rowList__item">
                        <button class="form__btn btn" type="button"
                            wire:click.prevent="selectJiri('{!! $jiri->slug !!}')">
                            <a><span>{!! $jiri->name !!}
                                </span></a>
                        </button>
                    </li>
                @endforeach
            </ul>
            @if ($selectedJiri && $showStartedJiris)
                <div class="home__startedJiris__jiri">
                    <h2 class="home__startedJiris__jiri__name card__second-title">{!! $selectedJiri->name !!}</h2>
                    <ul class="home__startedJiris__jiri__nav nav-button-list rowList">
                        <li class="home__startedJiris__jiri__nav__item nav-button-list__item rowList__item">
                            <button class="form__btn btn" type="button"
                                wire:click.prevent="startedJiriTab('attendances')">
                                <a><span>{!! __('button.attendances') !!}
                                    </span></a>
                            </button>
                        </li>
                        <li class="home__startedJiris__jiri__nav__item">
                            <button class="form__btn btn" type="button" wire:click.prevent="startedJiriTab('scores')">
                                <a><span>{!! __('button.scores') !!}
                                    </span></a>
                            </button>
                        </li>
                        <li class="home__startedJiris__jiri__nav__item">
                            <button class="form__btn btn" type="button"
                                wire:click.prevent="startedJiriTab('evaluations')">
                                <a><span>{!! __('button.ongoing_evaluations') !!}
                                    </span></a>
                            </button>
                        </li>
                    </ul>
                    @if ($showSelectedJiriAttendances)
                        <div class="home__startedJiris__jiri__attendances flex-list-hor">
                            @if ($this->selectedJiriAttendances['students']->isNotEmpty())
                                <div class="home__startedJiris__jiri__attendances__left">
                                    <h3 class="fz-20 mb-2">{!! __('title.students') !!}
                                    </h3>
                                    <ul class="list-500-hidden second-list">
                                        @foreach ($this->selectedJiriAttendances['students'] as $student)
                                            <li class="home__students-attendances__left__item second-list__item relative"
                                                wire:key="{!! $student->id !!}">
                                                <div class="second-list__item__infos">
                                                    <h3>{!! $student->firstname . ' ' . $student->lastname !!}</h3>
                                                    <div class="second-list__item__infos__projects">
                                                        @foreach ($student->projects as $project)
                                                            <button class="form__btn btn mr-2" type="button"><a
                                                                    wire:navigate
                                                                    href="/{!! $student->slug !!}/{!! $project->jiri_project->slug !!}"><span>{!! $project->jiri_project->name !!}</span></a></button>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <div class="second-list__item__avatar avatar">
                                                    <img width="100" height="100" src="{!! asset($student->image->image_url) . '?' . $student->image->updated_at->format('U') ?? '' !!}"
                                                        alt="{!! __('button.image_of') . $student->firstname . ' ' . $student->lastname !!}">
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if ($this->selectedJiriAttendances['evaluators']->isNotEmpty())
                                <div class="home__startedJiris__jiri__attendances__right">
                                    <h3 class="fz-20 mb-2">{!! __('title.evaluators') !!}
                                    </h3>
                                    <ul class="list-500-hidden second-list">
                                        @foreach ($this->selectedJiriAttendances['evaluators'] as $evaluator)
                                            <li class="home__students-attendances__left__item second-list__item relative"
                                                wire:key="{!! $evaluator->id !!}">
                                                <div class="second-list__item__infos">
                                                    <h3>{!! $evaluator->firstname . ' ' . $evaluator->lastname !!}</h3>

                                                </div>
                                                <div class="second-list__item__avatar avatar">
                                                    <img width="100" height="100" src="{!! asset($evaluator->image->image_url) . '?' . $evaluator->image->updated_at->format('U') ?? '' !!}"
                                                        alt="{!! __('button.image_of') . $evaluator->firstname . ' ' . $evaluator->lastname !!}">
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    @elseif ($showSelectedJiriScores)
                        <div class="home__startedJiris__jiri__scores scores">
                            <div class="scores__board__container">
                                <div class="scores__board">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th rowspan="2" class="sticky-index">
                                                    <p>Moyenne | Évaluateur (Projets) &rarr;</p>
                                                    <p>Étudiants &darr;</p>
                                                </th>
                                                <th rowspan="2">Projets<br>pondérés</th>
                                                <th colspan="{!! count($this->selectedJiriScores['selectedJiriProjects']) + 1 !!}">Moyenne</th>
                                                @foreach ($this->selectedJiriScores['evaluators'] as $evaluator)
                                                    <th colspan="{!! count($this->selectedJiriScores['selectedJiriProjects']) + 1 !!}" class="evaluator"
                                                        data-evaluator="{!! $evaluator->firstname . ' ' . $evaluator->lastname !!}">
                                                        <p>{!! $evaluator->firstname !!}</p>
                                                        <p>{!! $evaluator->lastname !!}</p>
                                                    </th>
                                                @endforeach
                                            </tr>
                                            <tr class="projects-columns">
                                                <th>Score Global</th>
                                                @foreach ($this->selectedJiriScores['selectedJiriProjects'] as $project)
                                                    <th class="cells">{{ $project->name }}</th>
                                                @endforeach
                                                @foreach ($this->selectedJiriScores['evaluators'] as $evaluator)
                                                    <th class="cells">Global Score</th>
                                                    @foreach ($this->selectedJiriScores['selectedJiriProjects'] as $project)
                                                        <th class="cells">{{ $project->name }}</th>
                                                    @endforeach
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($this->selectedJiriScores['students'] as $student)
                                                <tr>
                                                    <td class="student sticky-students"
                                                        data-student="{!! $student->firstname !!} {!! $student->lastname !!}">
                                                        <p>{!! $student->firstname !!}</p>
                                                        <p>{!! $student->lastname !!}</p>
                                                    </td>
                                                    @php
                                                        $weightedProjects = 0;
                                                        foreach ($this->selectedJiriScores['selectedJiriProjects'] as $project) {
                                                            $weightedProjects +=
                                                                number_format(
                                                                    $selectedJiri->projects_scores
                                                                        ->where('student_attendance_id', $student->attendances[0]->id)
                                                                        ->where('project_id', $student->projects->where('jiri_project_id', $project->id)->first()->id)
                                                                        ->sum('score') /
                                                                        $selectedJiri->projects_scores
                                                                            ->where('student_attendance_id', $student->attendances[0]->id)
                                                                            ->where('project_id', $student->projects->where('jiri_project_id', $project->id)->first()->id)
                                                                            ->count(),
                                                                    2,
                                                                ) * $project->weighting;
                                                        }
                                                        $weightedProjects = number_format($weightedProjects, 2);
                                                    @endphp
                                                    <td>{!! $weightedProjects !!}</td>
                                                    <td class="score">{!! number_format(
                                                        $selectedJiri->global_scores->where('student_attendance_id', $student->attendances[0]->id)->sum('global_score') /
                                                            $selectedJiri->global_scores->where('student_attendance_id', $student->attendances[0]->id)->count(),
                                                        2,
                                                    ) ?? 'N/A' !!}</td>
                                                    @foreach ($this->selectedJiriScores['selectedJiriProjects'] as $project)
                                                        <td class="score">{!! number_format(
                                                            $selectedJiri->projects_scores->where('student_attendance_id', $student->attendances[0]->id)->where('project_id', $student->projects->where('jiri_project_id', $project->id)->first()->id)->sum('score') /
                                                                $selectedJiri->projects_scores->where('student_attendance_id', $student->attendances[0]->id)->where('project_id', $student->projects->where('jiri_project_id', $project->id)->first()->id)->count(),
                                                            2,
                                                        ) !!}</td>
                                                    @endforeach
                                                    @foreach ($this->selectedJiriScores['evaluators'] as $evaluator)
                                                        <td wire:click.prevent="showEvaluation('{!! $student->attendances[0]->id !!}', '{!! $evaluator->attendances[0]->id !!}')"
                                                            class="score border"
                                                            data-evaluator="{!! $evaluator->firstname . ' ' . $evaluator->lastname !!}"
                                                            data-student="{!! $student->firstname . ' ' . $student->lastname !!}">
                                                            {!! number_format(
                                                                $selectedJiri->global_scores->where('student_attendance_id', $student->attendances[0]->id)->where('evaluator_attendance_id', $evaluator->attendances[0]->id)->first()->global_score,
                                                                2,
                                                            ) ?? 'N/A' !!}
                                                        </td>
                                                        @foreach ($this->selectedJiriScores['selectedJiriProjects'] as $project)
                                                            <td wire:click.prevent="showEvaluation('{!! $student->attendances[0]->id !!}', '{!! $evaluator->attendances[0]->id !!}')"
                                                                class="score border"
                                                                data-evaluator="{!! $evaluator->firstname . ' ' . $evaluator->lastname !!}"
                                                                data-student="{!! $student->firstname . ' ' . $student->lastname !!}">
                                                                {!! $selectedJiri->global_scores->where('student_attendance_id', $student->attendances[0]->id)->where('evaluator_attendance_id', $evaluator->attendances[0]->id)->first()->student->projects->where('jiri_project_id', $project->id)->first()
                                                                    ? number_format(
                                                                            $selectedJiri->global_scores->where('student_attendance_id', $student->attendances[0]->id)->where('evaluator_attendance_id', $evaluator->attendances[0]->id)->first()->student->projects->where('jiri_project_id', $project->id)->first()->scores->where('evaluator_attendance_id', $evaluator->attendances[0]->id)->first()->score,
                                                                            2,
                                                                        ) ?? 'N/A'
                                                                    : 'N/A' !!}
                                                            </td>
                                                        @endforeach
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @elseif ($showSelectedJiriOngoingEvaluations)
                        <div class="home__startedJiris__jiri__evaluations scores" wire:poll.2500ms>
                            <div>
                                <button class="btn" type="button">
                                    <a target="_blank"
                                        href="/live/{!! $selectedJiri->slug !!}"><span>{!! __('button.new_window') !!}
                                        </span></a>
                                </button>
                            </div>
                            <div class="scores__board__container">
                                <div class="scores__board">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th rowspan="2" class="sticky-index">
                                                    <p>Evaluator (Présentation) &rarr;</p>
                                                    <p>Student &darr;</p>
                                                </th>
                                                @foreach ($this->selectedJiriOngoingEvaluations['evaluators'] as $evaluator)
                                                    <th class="evaluator" data-evaluator="{!! $evaluator->firstname . ' ' . $evaluator->lastname !!}">
                                                        <p>{!! $evaluator->firstname !!}</p>
                                                        <p>{!! $evaluator->lastname !!}</p>
                                                    </th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($this->selectedJiriOngoingEvaluations['students'] as $student)
                                                <tr>
                                                    <td class="student sticky-students"
                                                        data-student="{!! $student->firstname !!} {!! $student->lastname !!}">
                                                        <p>{!! $student->firstname !!}</p>
                                                        <p>{!! $student->lastname !!}</p>
                                                    </td>
                                                    @foreach ($this->selectedJiriOngoingEvaluations['evaluators'] as $evaluator)
                                                        <td class="score border"
                                                            data-evaluator="{!! $evaluator->firstname . ' ' . $evaluator->lastname !!}"
                                                            data-student="{!! $student->firstname . ' ' . $student->lastname !!}">
                                                            <select class="form-control" name="status" id="status"
                                                                wire:change="updateEvaluationStatus('{!! $student->attendances->first()->id !!}', '{!! $evaluator->attendances->first()->id !!}', $event.target.value)">
                                                                <option value="to_present" {!! $this->selectedJiriOngoingEvaluations['ongoingEvaluations']->where('student_attendance_id', $student->attendances->first()->id)->where('evaluator_attendance_id', $evaluator->attendances->first()->id)->first() &&
                                                                $this->selectedJiriOngoingEvaluations['ongoingEvaluations']->where('student_attendance_id', $student->attendances->first()->id)->where('evaluator_attendance_id', $evaluator->attendances->first()->id)->first()->status === 'to_present'
                                                                    ? 'selected'
                                                                    : '' !!}>❌
                                                                </option>
                                                                <option value="ongoing" {!! $this->selectedJiriOngoingEvaluations['ongoingEvaluations']->where('student_attendance_id', $student->attendances->first()->id)->where('evaluator_attendance_id', $evaluator->attendances->first()->id)->first() &&
                                                                $this->selectedJiriOngoingEvaluations['ongoingEvaluations']->where('student_attendance_id', $student->attendances->first()->id)->where('evaluator_attendance_id', $evaluator->attendances->first()->id)->first()->status === 'ongoing'
                                                                    ? 'selected'
                                                                    : '' !!}>⏳
                                                                </option>
                                                                <option value="presented" {!! $this->selectedJiriOngoingEvaluations['ongoingEvaluations']->where('student_attendance_id', $student->attendances->first()->id)->where('evaluator_attendance_id', $evaluator->attendances->first()->id)->first() &&
                                                                $this->selectedJiriOngoingEvaluations['ongoingEvaluations']->where('student_attendance_id', $student->attendances->first()->id)->where('evaluator_attendance_id', $evaluator->attendances->first()->id)->first()->status === 'presented'
                                                                    ? 'selected'
                                                                    : '' !!}>✅
                                                                </option>
                                                            </select>
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    @endif
    {{-- @if (!$this->passedJiris->isEmpty())
        <div class="home__passedJiris card relative">
            <div wire:click.prevent="toggleShowPassedJiris"
                class="home__passedJiris__arrow arrow {!! $showPassedJiris ? 'arrow-up' : 'arrow-down' !!}">&#8595;</div>
            <ul class="home__passedJiris__list rowList">
                @foreach ($this->passedJiris as $jiri)
                    <li class="home__passedJiris__list__item rowList__item">
                        <button class="form__btn btn" type="button"
                            wire:click.prevent="selectPassedJiri('{!! $jiri->slug !!}')">
                            <a><span>{!! $jiri->name !!}
                                </span></a>
                        </button>
                    </li>
                @endforeach
            </ul>
            @if ($selectedPassedJiri && $showPassedJiris)
                <div class="home__passedJiris__jiri">
                    <h2 class="home__passedJiris__jiri__name card__second-title">{!! $selectedPassedJiri->name !!}</h2>
                    <ul class="home__passedJiris__jiri__nav rowList">
                        <li class="home__passedJiris__jiri__nav__item rowList__item">
                            <button class="form__btn btn" type="button"
                                wire:click.prevent="passedJiriTab('attendances')">
                                <a><span>{!! __('button.attendances') !!}
                                    </span></a>
                            </button>
                        </li>
                        <li class="home__passedJiris__jiri__nav__item">
                            <button class="form__btn btn" type="button"
                                wire:click.prevent="passedJiriTab('scores')">
                                <a><span>{!! __('button.scores') !!}
                                    </span></a>
                            </button>
                        </li>
                        <li class="home__passedJiris__jiri__nav__item">
                            <button class="form__btn btn" type="button"
                                wire:click.prevent="passedJiriTab('evaluations')">
                                <a><span>{!! __('button.ongoing_evalutions') !!}
                                    </span></a>
                            </button>
                        </li>
                    </ul>
                    @if ($showSelectedPassedJiriAttendances)
                        <div class="home__startedJiris__jiri__attendances flex-list-hor">
                            <div>
                                <h3>{!! __('title.students') !!}
                                </h3>
                                <ul class="list-500-hidden second-list">
                                    @foreach ($this->selectedPassedJiriAttendances['students'] as $student)
                                        <li class="home__students-attendances__left__item second-list__item relative"
                                            wire:key="{!! $student->id !!}">
                                            <div class="second-list__item__infos">
                                                <h3>{!! $student->firstname . ' ' . $student->lastname !!}</h3>
                                                <div class="second-list__item__infos__projects">
                                                    @foreach ($student->projects as $project)
                                                        <button class="form__btn btn" type="button"><a wire:navigate
                                                                href="/{!! $student->slug !!}/{!! $project->jiri_project->slug !!}"><span>{!! $project->jiri_project->name !!}</span></a></button>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div class="second-list__item__avatar avatar">
                                                <img width="100" height="100" src="{!! asset($student->image->image_url) . '?' . $student->image->updated_at->format('U') ?? '' !!}"
                                                    alt="{!! __('button.image_of') . $student->firstname . ' ' . $student->lastname !!}">
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div>
                                <h3>{!! __('title.evaluators') !!}
                                </h3>
                                <ul class="list-500-hidden second-list">
                                    @foreach ($this->selectedPassedJiriAttendances['evaluators'] as $evaluator)
                                        <li class="home__students-attendances__left__item second-list__item relative"
                                            wire:key="{!! $evaluator->id !!}">
                                            <div class="second-list__item__infos">
                                                <h3>{!! $evaluator->firstname . ' ' . $evaluator->lastname !!}</h3>

                                            </div>
                                            <div class="second-list__item__avatar avatar">
                                                <img width="100" height="100" src="{!! asset($evaluator->image->image_url) . '?' . $evaluator->image->updated_at->format('U') ?? '' !!}"
                                                    alt="{!! __('button.image_of') . $evaluator->firstname . ' ' . $evaluator->lastname !!}">
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @elseif ($showSelectedPassedJiriScores)
                        <div class="home__startedJiris__jiri__scores scores">
                            <div class="scores__board__container">
                                <div class="scores__board">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th rowspan="2" class="sticky-index">
                                                    <p>Moyenne | Évaluateur (Projets) &rarr;</p>
                                                    <p>Étudiants &darr;</p>
                                                </th>
                                                <th colspan="{!! count($this->selectedPassedJiriScores['selectedPassedJiriProjects']) + 1 !!}">Moyenne</th>
                                                @foreach ($this->selectedPassedJiriScores['evaluators'] as $evaluator)
                                                    <th colspan="{!! count($this->selectedPassedJiriScores['selectedPassedJiriProjects']) + 1 !!}" class="evaluator"
                                                        data-evaluator="{!! $evaluator->firstname . ' ' . $evaluator->lastname !!}">
                                                        <p>{!! $evaluator->firstname !!}</p>
                                                        <p>{!! $evaluator->lastname !!}</p>
                                                    </th>
                                                @endforeach
                                            </tr>
                                            <tr class="projects-columns">
                                                <th>Score Global</th>
                                                @foreach ($this->selectedPassedJiriScores['selectedPassedJiriProjects'] as $project)
                                                    <th class="cells">{{ $project->name }}</th>
                                                @endforeach
                                                @foreach ($this->selectedPassedJiriScores['evaluators'] as $evaluator)
                                                    <th class="cells">Global Score</th>
                                                    @foreach ($this->selectedPassedJiriScores['selectedPassedJiriProjects'] as $project)
                                                        <th class="cells">{{ $project->name }}</th>
                                                    @endforeach
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($this->selectedPassedJiriScores['students'] as $student)
                                                <tr>
                                                    <td class="student sticky-students"
                                                        data-student="{!! $student->firstname !!} {!! $student->lastname !!}">
                                                        <p>{!! $student->firstname !!}</p>
                                                        <p>{!! $student->lastname !!}</p>
                                                    </td>
                                                    <td>{!! $selectedJiri->global_scores->where('student_attendance_id', $student->attendances[0]->id)->sum('global_score') /
                                                        $selectedJiri->global_scores->where('student_attendance_id', $student->attendances[0]->id)->count() ??
                                                        'N/A' !!}</td>
                                                    @foreach ($this->selectedPassedJiriScores['selectedPassedJiriProjects'] as $project)
                                                        <td>{!! $selectedJiri->projects_scores->where('student_attendance_id', $student->attendances[0]->id)->where('project_id', $student->projects->where('jiri_project_id', $project->id)->first()->id)->sum('score') /
                                                            $selectedJiri->projects_scores->where('student_attendance_id', $student->attendances[0]->id)->where('project_id', $student->projects->where('jiri_project_id', $project->id)->first()->id)->count() !!}</td>
                                                    @endforeach
                                                    @foreach ($this->selectedPassedJiriScores['evaluators'] as $evaluator)
                                                        <td wire:click.prevent="showEvaluation('{!! $student->attendances[0]->id !!}', '{!! $evaluator->attendances[0]->id !!}')"
                                                            class="score border"
                                                            data-evaluator="{!! $evaluator->firstname . ' ' . $evaluator->lastname !!}"
                                                            data-student="{!! $student->firstname . ' ' . $student->lastname !!}">
                                                            {!! $selectedJiri->global_scores->where('student_attendance_id', $student->attendances[0]->id)->where('evaluator_attendance_id', $evaluator->attendances[0]->id)->first()->global_score ?? 'N/A' !!}
                                                        </td>
                                                        @foreach ($this->selectedPassedJiriScores['selectedPassedJiriProjects'] as $project)
                                                            <td wire:click.prevent="showEvaluation('{!! $student->attendances[0]->id !!}', '{!! $evaluator->attendances[0]->id !!}')"
                                                                class="score border"
                                                                data-evaluator="{!! $evaluator->firstname . ' ' . $evaluator->lastname !!}"
                                                                data-student="{!! $student->firstname . ' ' . $student->lastname !!}">
                                                                {!! $selectedJiri->global_scores->where('student_attendance_id', $student->attendances[0]->id)->where('evaluator_attendance_id', $evaluator->attendances[0]->id)->first()->student->projects->where('jiri_project_id', $project->id)->first()
                                                                    ? $selectedJiri->global_scores->where('student_attendance_id', $student->attendances[0]->id)->where('evaluator_attendance_id', $evaluator->attendances[0]->id)->first()->student->projects->where('jiri_project_id', $project->id)->first()->scores->where('evaluator_attendance_id', $evaluator->attendances[0]->id)->first()->score ?? 'N/A'
                                                                    : 'N/A' !!}
                                                            </td>
                                                        @endforeach
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @elseif ($showSelectedPassedJiriOngoingEvaluations)
                        <div class="home__startedJiris__jiri__evaluations scores">
                            <div class="scores__board__container">
                                <div class="scores__board">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th rowspan="2" class="sticky-index">
                                                    <p>Evaluator (Présentation) &rarr;</p>
                                                    <p>Student &darr;</p>
                                                </th>
                                                @foreach ($this->selectedPassedJiriOngoingEvaluations['evaluators'] as $evaluator)
                                                    <th class="evaluator" data-evaluator="{!! $evaluator->firstname . ' ' . $evaluator->lastname !!}">
                                                        <p>{!! $evaluator->firstname !!}</p>
                                                        <p>{!! $evaluator->lastname !!}</p>
                                                    </th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($this->selectedPassedJiriOngoingEvaluations['students'] as $student)
                                                <tr>
                                                    <td class="student sticky-students"
                                                        data-student="{!! $student->firstname !!} {!! $student->lastname !!}">
                                                        <p>{!! $student->firstname !!}</p>
                                                        <p>{!! $student->lastname !!}</p>
                                                    </td>
                                                    @foreach ($this->selectedPassedJiriOngoingEvaluations['evaluators'] as $evaluator)
                                                        <td class="score border"
                                                            data-evaluator="{!! $evaluator->firstname . ' ' . $evaluator->lastname !!}"
                                                            data-student="{!! $student->firstname . ' ' . $student->lastname !!}">
                                                            @switch($this->selectedPassedJiriOngoingEvaluations['ongoingEvaluations']->where('student_attendance_id',
                                                                $student->attendances->first()->id)->where('evaluator_attendance_id',
                                                                $evaluator->attendances->first()->id)->first()->status ??
                                                                null)
                                                                @case('to_present')
                                                                    ❌
                                                                @break

                                                                @case('ongoing')
                                                                    ⏳
                                                                @break

                                                                @case('presented')
                                                                    ✅
                                                                @break

                                                                @default
                                                                    ❌
                                                            @endswitch
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    @endif --}}
    @if ($showEvaluationModal)
        <div class="modal">
            <div class="modal__over" @click.away="@this.call('hideEvaluation')">
                <div class="modal__over__content">
                    <div class="flex-inline-sb-top">
                        <h3 class="modal__title">{!! __('title.evaluation_of_by', [
                            'student' => $evaluation['global_score']->student->firstname . ' ' . $evaluation['global_score']->student->lastname,
                            'evaluator' =>
                                $evaluation['global_score']->evaluator->firstname . ' ' . $evaluation['global_score']->evaluator->lastname,
                        ]) !!}
                        </h3>
                        <div class="modal-title-exit">
                            <span wire:click.prevent="hideEvaluation" class="modal__exit">X</span>
                        </div>
                    </div>
                    <div class="evaluation">
                        <div class="evaluation__global-score">
                            <h4>{!! __('title.global_score') !!}</h4>
                            <p><b>{!! __('title.global_score') !!} :</b> {!! $evaluation['global_score']->global_score !!}/20</p>
                            <p><b>{!! __('title.global_comment') !!} :</b> {!! $evaluation['global_score']->global_comment ?? 'N/A' !!}</p>
                        </div>
                        <div class="evaluation__projects-scores">
                            @foreach ($evaluation['projects_scores'] as $project)
                                <div class="evaluation__projects-scores__item">
                                    @if ($project)
                                        <h4>{!! $project->project->jiri_project->name !!}</h4>
                                        <p><b>{!! __('title.project_score') !!} :</b> {!! $project->score !!}/20
                                        </p>
                                        <p><b>{!! __('title.project_comment') !!} :</b> {!! $project->comment ?? 'N/A' !!}
                                        </p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if (auth()->user()->jiris->isEmpty())
        <div class="not-found card">
            <div class="home__empty__jiris">
                <p>{!! __('title.wish_create_jiri') !!}
                </p>
                <div class="mt-2">
                    <button class="btn" type="button" wire:click.prevent="toggleAddJiriModal">
                        <a><span>{!! __('button.create_jiri') !!}
                            </span></a>
                    </button>
                </div>
            </div>
        </div>
        @if ($showAddJiriModal)
            <div class="home__modal modal">
                @livewire('jiris.add-jiri-modal')
            </div>
        @endif
    @endif
    @if (auth()->user()->contacts->isEmpty())
        <div class="not-found card">
            <p>{!! __('title.wish_add_contact') !!}
            </p>
            <div class="mt-2">
                <button class="btn" type="button" wire:click.prevent="toggleAddContactModal">
                    <a><span>{!! __('button.add_contact') !!}
                        </span></a>
                </button>
            </div>
        </div>
        @if ($showAddContactModal)
            <div class="home__modal modal">
                @livewire('contacts.add-contact-modal')
            </div>
        @endif
    @endif
    @if (
        !auth()->user()->jiris->isEmpty() &&
            !auth()->user()->contacts->isEmpty() &&
            $this->startedJiris->isEmpty() &&
            $this->passedJiris->isEmpty())
        <div class="not-found card">
            <p>{!! __('title.started_jiri_needed') !!}
            </p>
            <div class="flex-inline-sb-mt">
                <button class="btn" type="button">
                    <a href="/jiris" wire:navigate><span>{!! __('button.start_a_jiri') !!}
                        </span></a>
                </button>
            </div>
        </div>
    @endif
</div>
