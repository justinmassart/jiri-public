<div class="student">
    <x-slot:title>
        {!! session()->get('jiri')->name !!}
    </x-slot:title>
    <x-slot:page_title>
        {!! session()->get('jiri')->name . ' - ' . $student->firstname . ' ' . $student->lastname !!}
    </x-slot:page_title>
    <div class="history">
        <span><a wire:navigate href="{!! route('evaluator.dashboard', ['jiri' => $jiri->slug]) !!}">{!! Str::slug($jiri->name) !!}</a>/<a wire:navigate
                class="underlined" href="{!! route('show.student', ['jiri' => $jiri->slug, 'student' => $student]) !!}">{!! $student->slug !!}</a></span>
    </div>
    <div class="student__left__infos card flex-inline-sb-nm">
        <div class="flex-column-sb">
            <h3>{!! $student->firstname . ' ' . $student->lastname !!}</h3>
            <p>{!! $student->email !!}</p>
        </div>
        <div class="avatar">
            <img width="100" height="100" src="{!! asset($student->image->image_url) . '?' . $student->image->updated_at->format('U') ?? '' !!}" alt="{!! __('button.image_of') . $student->firstname . ' ' . $student->lastname !!}">
        </div>
    </div>
    <div class="student__grid">
        <div class="student__left">
            <div class="student__left__score card">
                <div class="flex-inline-sb">
                    <h4>{!! __('title.global_score') !!}</h4>
                </div>
                <div class="student__left__score__container score__container">
                    <input id="rangeInput" wire:model.live.debounce.1000ms="globalScore" type="range" min="0"
                        max="20" step="0.5" id="scoreSlider" class="scoreSlider{!! !$this->canEvaluateStudent ? ' opacity-25' : '' !!}"
                        {!! !$this->canEvaluateStudent ? 'disabled' : '' !!} title="{!! !$this->canEvaluateStudent ? __('input.not_allowed_global_score') : __('input.allowed_global_score') !!}">
                    <div class="student__left__score__container__score score__container__score">
                        <input id="numberInput" class="scoreSlider__score{!! !$this->canEvaluateStudent ? ' opacity-25' : '' !!}" type="number"
                            min="0" max="20" step="0.01" wire:model.live.debounce.1000ms="globalScore"
                            {!! !$this->canEvaluateStudent ? 'disabled' : '' !!} title="{!! !$this->canEvaluateStudent ? __('input.not_allowed_global_score') : __('input.allowed_global_score') !!}" /><span id="twenty">/20</span>
                    </div>
                </div>
            </div>
            <div class="student__left__appreciation appreciation card">
                <div class="flex-inline-sb">
                    <h4>{!! __('title.global_comment') !!}</h4>
                </div>
                <textarea wire:model.live.debounce.1000ms="comment" name="studentAppreciation" id="studentAppreciation" cols="30"
                    rows="10" maxlength="750" placeholder="{!! __('input.appreciation') !!}" title="{!! !$this->canEvaluateStudent ? __('input.not_allowed_global_comment') : __('input.allowed_global_comment') !!}"
                    class="{!! !$this->canEvaluateStudent ? 'opacity-25' : '' !!}" {!! !$this->canEvaluateStudent ? 'disabled' : '' !!}></textarea>
            </div>
        </div>
        <div class="student__middle {!! !$this->canStartEvaluation ? 'opacity-25' : '' !!}">
            <div class="chrono {!! auth()->user()->evaluator_ongoing_evaluations->where('jiri_id', $jiri->id)->where('student_attendance_id', $student->attendances->where('jiri_id', $jiri->id)->first()->id)->first()->status === 'ongoing'
                ? 'chrono__current'
                : '' !!}">
                @if (auth()->user()->evaluator_ongoing_evaluations->where('jiri_id', $jiri->id)->where('student_attendance_id', $student->attendances->where('jiri_id', $jiri->id)->first()->id)->first()->status === 'ongoing')
                    <div class="chrono__stop" wire:click.prevent="toggleStopEvaluationModal">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 50">
                            <rect x="15" y="15" width="20" height="20" fill="none" stroke="white"
                                stroke-width="2" />
                        </svg>
                    </div>
                @else
                    <div class="chrono__start" {!! !$this->canStartEvaluation ? '' : "wire:click.prevent='toggleStartEvaluationModal'" !!}>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 50">
                            <polygon points="20,15 35,25 20,35" fill="none" stroke="white" stroke-width="2" />
                        </svg>
                    </div>
                @endif
            </div>
            {{--             @persist('timer')
                <div id="timer" class="chrono__timer">

                </div>
            @endpersist --}}
        </div>
        <div class="student__right card">
            <div class="flex-inline-sb">
                <h4>{!! __('title.projects') !!}
                </h4>
            </div>
            <ul class="student__right__projects-list other-projects">
                @foreach ($student->projects->whereNotNull('urls') as $project)
                    <li class="other-projects__item">
                        <button class="form__btn btn" type="button"><a wire:navigate
                                href="/{!! $jiri->slug !!}/{!! $student->slug !!}/{!! $project->jiri_project->slug !!}"><span>{!! $project->jiri_project->name !!}</span></a></button>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    <div class="student__other-students">
        <div class="student__other-students__top card">
            <div class="flex-inline-sb-nm-all">
                <h4>{!! __('title.other_students') !!}</h4>
                <div class="search{!! $search ? '-locked' : '' !!}">
                    <x-input :type="'search'" :labelFor="'contact'" :name="'search'" :wireModel="'search'" :required=false />
                </div>
            </div>
        </div>
        <div class="student__other-students__list-container">
            <ul class="student__other-students__list">
                @foreach ($this->otherStudents as $otherStudent)
                    <li class="student__other-students__list__item card pointer relative">
                        <a wire:navigate class="link" href="/{!! $jiri->slug !!}/{!! $otherStudent->slug !!}"></a>
                        <div class="avatar-big">
                            <img width="150" height="150" src="{!! asset($otherStudent->image->image_url) . '?' . $otherStudent->image->updated_at->format('U') ?? '' !!}"
                                alt="{!! __('button.image_of') . $otherStudent->firstname . ' ' . $otherStudent->lastname !!}">
                        </div>
                        <div class="other-student__name">
                            <p>{!! $otherStudent->firstname !!}</p>
                            <p>{!! $otherStudent->lastname !!}</p>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    @if ($showStartEvaluationModal)
        <div class="modal">
            <div class="modal__content">
                <div class="modal__content__container">
                    <div class="flex-inline-sb">
                        <h3 class="modal__title">{!! __('title.start_evaluation') !!}
                        </h3>
                        <span wire:click.prevent="toggleStartEvaluationModal" class="modal__exit">X</span>
                    </div>
                    <div class="modal__over__form">
                        <form class="form" wire:submit.prevent="startChrono">
                            <p class="fz-20">Êtes-vous prêt à démarrer l'évaluation de {!! $student->firstname . ' ' . $student->lastname !!} ?</p>
                            <div class="flex-inline-sb-mt">
                                <div class="delete-form__btn">
                                    <x-button :type="'submit'" :translationKey="'confirm'" />
                                </div>
                                <div class="delete-form__btn">
                                    <button wire:click.prevent="toggleStartEvaluationModal" class="form__btn btn"
                                        type="button"><a><span>{!! __('button.cancel') !!}
                                            </span></a></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if ($showStopEvaluationModal)
        <div class="modal">
            <div class="modal__content">
                <div class="modal__content__container">
                    <div class="flex-inline-sb">
                        <h3 class="modal__title">{!! __('title.stop_evaluation') !!}
                        </h3>
                        <span wire:click.prevent="toggleStopEvaluationModal" class="modal__exit">X</span>
                    </div>
                    <div class="modal__over__form">
                        <form class="form" wire:submit.prevent="stopChrono">
                            <p class="fz-20">Êtes-vous prêt à arrêter l'évaluation de {!! $student->firstname . ' ' . $student->lastname !!} ?</p>
                            @if ($projectsNotScored)
                                <div class="mt-2">
                                    <span class="error">{!! __('title.no_projects_scored') !!}
                                    </span>
                                    @foreach ($projectsNotScored as $project)
                                        <div class="fz-20 mt-2">
                                            <button class="form__btn btn" type="button"><a wire:navigate
                                                    href="/{!! $jiri->slug !!}/{!! $student->slug !!}/{!! $project->slug !!}"><span>{!! $project->name !!}
                                                    </span></a></button>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            @if (!$isGlobalScored)
                                <div class="mt-2">
                                    <span class="error">{!! __('title.no_global_score') !!}</span>
                                </div>
                            @endif
                            <div class="flex-inline-sb-mt">
                                <div class="delete-form__btn">
                                    <x-button :type="'submit'" :translationKey="'confirm'" />
                                </div>
                                <div class="delete-form__btn">
                                    <button wire:click.prevent="toggleStopEvaluationModal" class="form__btn btn"
                                        type="button"><a><span>{!! __('button.cancel') !!}
                                            </span></a></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    document.addEventListener('livewire:navigated', function() {
        Alpine.nextTick(() => {
            document.getElementById('rangeInput').addEventListener('input', function() {
                document.getElementById('numberInput').value = this.value;
            });
        });
    });
</script>
