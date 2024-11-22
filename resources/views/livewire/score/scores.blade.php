<div class="scores">
    <x-slot:title>
        {!! __('title.scores') . ' | Jiri' !!}
    </x-slot:title>
    <x-slot:page_title>
        {!! __('title.scores') !!}
    </x-slot:page_title>
    @if (auth()->user()->jiris->isNotEmpty())
        <div class="sorts__actions card">
            <div class="sorts__actions__filters">
                <div class="sorts__actions__filters__sort" x-data="{ open: false }">
                    <div class="sorts__actions__filters__sort__btn">
                        <x-button-modal :type="'button'" :translationKey="'sort'" :displayBehavior=true />
                    </div>
                    <div class="sorts__actions__filters__sort__modal filter-modal" x-show="open"
                        @click.away="open = false" style="display: none">
                        <span class="filter-modal__title">{!! __('button.sort') !!}
                        </span>
                        <div class="filter-modal__sort">
                            <h3>{!! __('input.creation_date') !!}</h3>
                            <div>
                                <span wire:click.prevent="setSort('starts_at', 'asc')">&uarr;</span>
                                <span wire:click.prevent="setSort('starts_at', 'desc')">&darr;</span>
                            </div>
                        </div>
                        <div class="filter-modal__sort">
                            <h3>{!! __('input.finish_date') !!}</h3>
                            <div>
                                <span wire:click.prevent="setSort('ends_at', 'asc')">&uarr;</span>
                                <span wire:click.prevent="setSort('ends_at', 'desc')">&darr;</span>
                            </div>
                        </div>
                        <div class="filter-modal__sort">
                            <h3>{!! __('input.name') !!}</h3>
                            <div>
                                <span wire:click.prevent="setSort('name', 'asc')">&uarr;</span>
                                <span wire:click.prevent="setSort('name', 'desc')">&darr;</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="sorts__actions__filters__search{!! $search ? '-locked' : '' !!}">
                    <x-input :type="'search'" :labelFor="'contact'" :name="'search'" :wireModel="'search'" :required=true />
                </div>
            </div>
        </div>
        @if ($this->jiris->isNotEmpty())
            <div class="scores__jiris">
                <div class="main__list" x-data="{ open: '{!! $showModal !!}' }">
                    @foreach ($this->jiris as $jiri)
                        <div class="main__list__item" wire:key="{!! $jiri->id !!}">
                            <a class="main__list__item__link"
                                wire:click.prevent="setSelectedJiri('{!! $jiri->slug !!}');" tabindex="0"><span
                                    class="hidden">{!! __('button.go_to_profile') . $jiri->firstname . ' ' . $jiri->lastname !!}</span></a>
                            <div class="main__list__item__infos">
                                <h3>{!! $jiri->name !!}</h3>
                                <p>{!! $jiri->attendances->count() . ' ' . __('title.participants') !!}</p>
                                <div class="flex-inline-sb">
                                    <div>{!! __('title.created_at', ['date' => date('d/m/Y', strtotime($jiri->starts_at))]) !!}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    @if ($selectedJiri)
                        <div class="scores__jiris__modal modal">
                            <div class="modal__content">
                                <div class="modal__content__container">
                                    <div class="flex-inline-sb">
                                        <h2 class="modal__title">{!! $jiri->name !!}</h2>
                                        <span wire:click.prevent="unsetSelectedJiri" class="modal__exit">X</span>
                                    </div>
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
                                                        <th colspan="{!! count($selectedJiriProjects) + 1 !!}">Moyenne</th>
                                                        @foreach ($evaluators as $evaluator)
                                                            <th colspan="{!! count($selectedJiriProjects) + 1 !!}" class="evaluator"
                                                                data-evaluator="{!! $evaluator->firstname . ' ' . $evaluator->lastname !!}">
                                                                <p>{!! $evaluator->firstname !!}</p>
                                                                <p>{!! $evaluator->lastname !!}</p>
                                                            </th>
                                                        @endforeach
                                                    </tr>
                                                    <tr class="projects-columns">
                                                        <th>M. Score Global</th>
                                                        @foreach ($selectedJiriProjects as $project)
                                                            <th class="cells"> M. {{ $project->name }}</th>
                                                        @endforeach
                                                        @foreach ($evaluators as $evaluator)
                                                            <th class="cells">Score Global</th>
                                                            @foreach ($selectedJiriProjects as $project)
                                                                <th class="cells">{{ ucfirst($project->name) }}</th>
                                                            @endforeach
                                                        @endforeach
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($students as $student)
                                                        <tr>
                                                            <td class="student sticky-students"
                                                                data-student="{!! $student->firstname !!} {!! $student->lastname !!}">
                                                                <p>{!! $student->firstname !!}</p>
                                                                <p>{!! $student->lastname !!}</p>
                                                            </td>
                                                            @php
                                                                $weightedProjects = 0;
                                                                foreach ($selectedJiriProjects as $project) {
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
                                                            @foreach ($selectedJiriProjects as $project)
                                                                <td class="score">{!! number_format(
                                                                    $selectedJiri->projects_scores->where('student_attendance_id', $student->attendances[0]->id)->where('project_id', $student->projects->where('jiri_project_id', $project->id)->first()->id)->sum('score') /
                                                                        $selectedJiri->projects_scores->where('student_attendance_id', $student->attendances[0]->id)->where('project_id', $student->projects->where('jiri_project_id', $project->id)->first()->id)->count(),
                                                                    2,
                                                                ) ?? 'N/A' !!}</td>
                                                            @endforeach
                                                            @foreach ($evaluators as $evaluator)
                                                                <td wire:click.prevent="showEvaluation('{!! $student->slug !!}', '{!! $evaluator->slug !!}')"
                                                                    class="score border"
                                                                    data-evaluator="{!! $evaluator->firstname . ' ' . $evaluator->lastname !!}"
                                                                    data-student="{!! $student->firstname . ' ' . $student->lastname !!}">
                                                                    {!! number_format(
                                                                        $selectedJiri->global_scores->where('student_attendance_id', $student->attendances[0]->id)->where('evaluator_attendance_id', $evaluator->attendances[0]->id)->first()->global_score,
                                                                        2,
                                                                    ) ?? 'N/A' !!}
                                                                </td>
                                                                @foreach ($selectedJiriProjects as $project)
                                                                    <td wire:click.prevent="showEvaluation('{!! $student->slug !!}', '{!! $evaluator->slug !!}')"
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
                            </div>
                        </div>
                    @endif
                    @if ($showEvaluationModal)
                        <div class="modal">
                            <div class="modal__over" @click.away="@this.call('hideEvaluation')">
                                <div class="modal__over__content">
                                    <div class="flex-inline-sb">
                                        <h3 class="modal__title">{!! __('title.evaluation_of_by', [
                                            'student' => $evaluation['global_score']->student->firstname . ' ' . $evaluation['global_score']->student->lastname,
                                            'evaluator' =>
                                                $evaluation['global_score']->evaluator->firstname . ' ' . $evaluation['global_score']->evaluator->lastname,
                                        ]) !!}
                                        </h3>
                                        <span wire:click.prevent="toggleShowEvaluationModal"
                                            class="modal__exit">X</span>
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
                </div>
                {{ $this->jiris->links() }}
            </div>
        @elseif($this->jiris->isEmpty() && $search)
            <div class="not-found card">
                <p>{!! __('title.search_not_found', ['search' => $search]) !!}
                </p>
            </div>
        @else
            <div class="not-found card">
                <p>{!! __('title.no_jiri_started') !!}</p>
                <div class="mt-2">
                    <button class="form__btn btn" type="button">
                        <a href="/jiris" wire:navigate><span>{!! __('button.start_a_jiri') !!}
                            </span></a>
                    </button>
                </div>
            </div>
        @endif
    @else
        <div class="not-found card">
            <p>{!! __('title.no_jiri_started') !!}</p>
            <div class="mt-2">
                <button class="form__btn btn" type="button">
                    <a href="/jiris" wire:navigate><span>{!! __('button.start_a_jiri') !!}
                        </span></a>
                </button>
            </div>
        </div>
    @endif
</div>
