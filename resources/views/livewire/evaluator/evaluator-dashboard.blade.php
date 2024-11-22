<div class="home" wire:init="welcome">
    <x-slot:title>
        {!! $selectedJiri->name !!}
    </x-slot:title>
    <x-slot:page_title>
        {!! $selectedJiri->name !!}
    </x-slot:page_title>
    <div class="home__students-attendances">
        @if ($this->presentations['left'])
            <div class="all">
                <h2 class="card fz-20 mb-2">{!! __('title.students_to_evaluate') !!}</h2>
                <div class="home__students-attendances__left">
                    <ul class="second-list">
                        @foreach ($this->presentations['left'] as $presentation)
                            <li class="home__students-attendances__left__item second-list__item relative"
                                wire:key="{!! $presentation->id !!}">
                                <a wire:navigate class="link" href="/{!! $selectedJiri->slug . '/' . $presentation->student->slug !!}"></a>
                                <div class="second-list__item__infos">
                                    <h3>{!! $presentation->student->firstname . ' ' . $presentation->student->lastname !!}</h3>
                                    <div class="second-list__item__infos__projects">
                                        @foreach ($presentation->student->projects->whereNotNull('urls') as $project)
                                            <button class="form__btn btn mr-2" type="button"><a wire:navigate
                                                    href="/{!! $selectedJiri->slug !!}/{!! $presentation->student->slug !!}/{!! $project->jiri_project->slug !!}"><span>{!! $project->jiri_project->name !!}</span></a></button>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="second-list__item__avatar avatar">
                                    <img width="100" height="100" src="{!! asset($presentation->student->image->image_url) .
                                        '?' .
                                        $presentation->student->image->updated_at->format('U') ??
                                        '' !!}"
                                        alt="{!! __('button.image_of') . $presentation->student->firstname . ' ' . $presentation->student->lastname !!}">
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @else
            <div class="card fz-20">
                <p>{!! __('title.no_students_left') !!}
                </p>
            </div>
        @endif
        @if ($this->presentations['passed'])
            <div class="all">
                <h2 class="card fz-20">{!! __('title.students_attendances_passed') !!}</h2>
                <div class="home__students-attendances__passed">
                    <ul class="second-list">
                        @foreach ($this->presentations['passed'] as $presentation)
                            <li class="home__students-attendances__passed__item second-list__item relative"
                                wire:key="{!! $presentation->id !!}">
                                <a wire:navigate class="link"
                                    href="/{!! $selectedJiri->slug !!}/{!! $presentation->student->slug !!}"></a>
                                <div class="second-list__item__infos">
                                    <h3>{!! $presentation->student->firstname . ' ' . $presentation->student->lastname !!}</h3>
                                    <div class="second-list__item__infos__projects">
                                        @foreach ($presentation->student->projects->whereNotNull('urls') as $project)
                                            <button class="form__btn btn mr-2" type="button"><a wire:navigate
                                                    href="/{!! $selectedJiri->slug !!}/{!! $presentation->student->slug !!}/{!! $project->jiri_project->slug !!}"><span>{!! $project->jiri_project->name !!}</span></a></button>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="second-list__item__avatar avatar">
                                    <img width="100" height="100" src="{!! asset($presentation->student->image->image_url) .
                                        '?' .
                                        $presentation->student->image->updated_at->format('U') ??
                                        '' !!}"
                                        alt="{!! __('button.image_of') . $presentation->student->firstname . ' ' . $presentation->student->lastname !!}">
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @else
            <div class="card fz-20">
                <p>{!! __('title.no_students_passed') !!}
                </p>
            </div>
        @endif
    </div>
    <div class="footer-btn mt-2">
        <button class="btn" type="button" wire:click.prevent="toggleFinishModal">
            <a><span>{!! __('button.finish_evaluating') !!}</span></a>
        </button>
    </div>
    @if ($showFinishModal)
        <div class="modal">
            <div class="modal__content">
                <div class="modal__content__container">
                    <div class="flex-inline-sb">
                        <h3 class="modal__title">{!! __('title.leave_session') !!}
                        </h3>
                        <span wire:click.prevent="toggleFinishModal" class="modal__exit">X</span>
                    </div>
                    <div class="modal__over__form">
                        <form class="form" wire:submit.prevent="terminateEvaluation">
                            <p class="fz-20">Êtes-vous sûr de vouloir quitter votre session ?</p>
                            <p class="fz-20 mt-2">Cela mettra fin à votre capacité à vous connecter à ce jiri et donc
                                vous
                                ne pourrez plus
                                évaluer des étudiants. Vos évaluations seront alors rendues publiques à l’administrateur
                                de ce jiri.</p>
                            @if ($this->presentations['left'] && $this->presentations['left']->isNotEmpty())
                                <div class="mt-2">
                                    <span class="error fz-20 mt-4">Il vous reste encore ce/ces étudiant•e•s à évaluer
                                        :</span>
                                </div>
                                <ul class="second-list mt-2">
                                    @foreach ($this->presentations['left'] as $presentation)
                                        <li class="home__students-attendances__left__item second-list__item relative"
                                            wire:key="{!! $presentation->id !!}">
                                            <a wire:navigate class="link" href="/{!! $selectedJiri->slug . '/' . $presentation->student->slug !!}"></a>
                                            <div class="second-list__item__infos">
                                                <h3>{!! $presentation->student->firstname . ' ' . $presentation->student->lastname !!}</h3>
                                                <div class="second-list__item__infos__projects">
                                                    @foreach ($presentation->student->projects->whereNotNull('urls') as $project)
                                                        <button class="form__btn btn mr-2" type="button"><a
                                                                wire:navigate
                                                                href="/{!! $selectedJiri->slug !!}/{!! $presentation->student->slug !!}/{!! $project->jiri_project->slug !!}"><span>{!! $project->jiri_project->name !!}</span></a></button>
                                                    @endforeach
                                                </div>`
                                            </div>
                                            <div class="second-list__item__avatar avatar">
                                                <img width="100" height="100" src="{!! asset($presentation->student->image->image_url) .
                                                    '?' .
                                                    $presentation->student->image->updated_at->format('U') ??
                                                    '' !!}"
                                                    alt="{!! __('button.image_of') . $presentation->student->firstname . ' ' . $presentation->student->lastname !!}">
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                                @if ($studentsWithNoGlobalScore)
                                    <div class="mt-2">
                                        <span class="error fz-20 mt-4">Vous n’avez pas attribué de cote globale à/aux
                                            étudiant•e•s suivant•e•s</span>
                                    </div>
                                    <ul class="second-list mt-2">
                                        @foreach ($studentsWithNoGlobalScore as $student)
                                            <li class="home__students-attendances__left__item second-list__item relative"
                                                wire:key="{!! $student->id !!}">
                                                <a wire:navigate class="link" href="/{!! $selectedJiri->slug . '/' . $student->slug !!}"></a>
                                                <div class="second-list__item__infos">
                                                    <h3>{!! $student->firstname . ' ' . $student->lastname !!}</h3>
                                                </div>
                                                <div class="second-list__item__avatar avatar">
                                                    <img width="100" height="100" src="{!! asset($student->image->image_url) . '?' . $student->image->updated_at->format('U') ?? '' !!}"
                                                        alt="{!! __('button.image_of') . $student->firstname . ' ' . $student->lastname !!}">
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                                @if ($projectsWithNoScore)
                                    <div class="mt-2">
                                        <span class="error fz-20 mt-4">Vous n’avez pas attribué de cote au(x) projet(s)
                                            suivant(s)</span>
                                    </div>
                                    <ul class="second-list mt-2">
                                        @foreach ($projectsWithNoScore as $student => $projects)
                                            <li class="home__students-attendances__left__item second-list__item relative"
                                                wire:key="{!! $student !!}">
                                                <div class="second-list__item__infos">
                                                    @php
                                                        $student = explode('-', $student);
                                                    @endphp
                                                    <h3>{!! ucfirst($student[0]) . ' ' . ucfirst($student[1]) !!}</h3>
                                                    <div class="second-list__item__infos__projects">
                                                        @foreach ($projects as $ps)
                                                            <button class="form__btn btn mr-2" type="button"><a
                                                                    wire:navigate
                                                                    href="/{!! $selectedJiri->slug !!}/{!! $ps->student->slug !!}/{!! $ps->project->jiri_project->slug !!}"><span>{!! $ps->project->jiri_project->name !!}</span></a></button>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <div class="second-list__item__avatar avatar">
                                                    <img width="100" height="100" src="{!! asset($ps->student->image->image_url) . '?' . $ps->student->image->updated_at->format('U') ?? '' !!}"
                                                        alt="{!! __('button.image_of') . $ps->student->firstname . ' ' . $ps->student->lastname !!}">
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            @endif
                            <div class="flex-inline-sb-mt">
                                <div class="delete-form__btn">
                                    <button class="form__btn btn" type="submit">
                                        <a><span>{!! __('button.confirm') !!}</span></a>
                                    </button>
                                </div>
                                <div class="delete-form__btn">
                                    <button class="form__btn btn" type="button"
                                        wire:click.prevent="toggleFinishModal">
                                        <a><span>{!! __('button.cancel') !!}
                                            </span></a>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
