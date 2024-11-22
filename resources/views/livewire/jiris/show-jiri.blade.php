<div class="modal__content">
    @if ($jiri)
        <div class="modal__content__container" x-data="{ open: false }">
            <div class="modal-title-exit-col">
                <div class="flex-title-btns">
                    <div>
                        <h2 class="modal__title">{!! $jiri->name !!}</h2>
                        @if ($hasStarted)
                            <span class="started__title">{!! $hasStarted ? __('title.started') : '' !!}</span>
                        @endif
                        <span wire:click.prevent="resetSelectedJiri; open = false;" class="modal__exit">X</span>
                    </div>
                    <div class="mt-2">
                        @if ($hasStarted)
                            <button class="form__btn btn" type="button" wire:click.prevent="toggleStopJiriModal">
                                <a><span>{!! __('button.stop') !!}
                                    </span></a>
                            </button>
                        @else
                            <button class="form__btn btn" type="button" wire:click.prevent="toggleStartJiriModal"
                                {!! $this->canStartJiri ? '' : 'disabled' !!}>
                                <a><span>{!! __('button.start') !!}
                                    </span></a>
                            </button>
                            <button class="form__btn btn" type="button" wire:click.prevent="exportJiri">
                                <a><span>{!! __('button.export_jiri') !!}</span></a>
                            </button>
                            <button class="btn" type="button"
                                onclick="document.getElementById('importJiri').click();">
                                <a><span>{!! __('button.import_jiri') !!}</span></a>
                            </button>
                            <input wire:model.live="jiriFile" type="file" accept=".xlsx" id="importJiri"
                                style="display: none;" onchange="handleFileImport(this)">
                            <button class="form__btn btn" type="button" wire:click.prevent="toggleDeleteJiriModal">
                                <a><span>{!! __('button.delete') !!}
                                    </span></a>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            <div class="modal__forms">
                <form class="form jiri__modal__form jiri__contacts__form border-container">
                    <div class="modal__form__title">
                        <h3 class="form__title">{!! __('title.participants') !!}</h3>
                        <div class="jiri__contacts__form__filters">
                            <div class="sorts__actions__filters">
                                <div class="sorts__actions__filters__sort sorts__item" x-data="{ open: false }">
                                    <div class="sorts__actions__filters__sort__btn">
                                        <x-button-modal :type="'button'" :translationKey="'sort'" :displayBehavior=true />
                                    </div>
                                    <div class="sorts__actions__filters__sort__modal filter-modal" x-show="open"
                                        @click.away="open = false" style="display: none">
                                        <span class="filter-modal__title">{!! __('button.sort') !!}
                                        </span>
                                        <div class="filter-modal__sort">
                                            <h3>{!! __('input.firstname') !!}</h3>
                                            <div>
                                                <span
                                                    wire:click.prevent="setContactSort('firstname', 'asc')">&uarr;</span>
                                                <span
                                                    wire:click.prevent="setContactSort('firstname', 'desc')">&darr;</span>
                                            </div>
                                        </div>
                                        <div class="filter-modal__sort">
                                            <h3>{!! __('input.lastname') !!}</h3>
                                            <div>
                                                <span
                                                    wire:click.prevent="setContactSort('lastname', 'asc')">&uarr;</span>
                                                <span
                                                    wire:click.prevent="setContactSort('lastname', 'desc')">&darr;</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="sorts__actions__filters__search{!! $contactSearch ? '-locked' : '' !!}">
                                    <x-input :type="'search'" :labelFor="'contact'" :name="'search'" :wireModel="'contactSearch'"
                                        :required=false />
                                </div>
                                @if ($jiri->status !== 'started')
                                    <div class="form__btn btn sorts__item">
                                        <button class="form__btn btn" type="button"
                                            wire:click.prevent="toggleEditContactMode">
                                            <a><span>{!! __('button.edit') !!}
                                                </span></a>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="modal__form__list__container">
                        @if ($this->contacts->isEmpty() && $contactSearch)
                            <div class="no__results">
                                <h3>{!! __('title.no_results') !!}</h3>
                            </div>
                        @elseif ($this->contacts->isNotEmpty())
                            <ul class="jiri__attendances__list{!! $editContactMode ? '-edit' : '' !!}">
                                @if ($editContactMode)
                                    <li class="no_reduce" wire:click.prevent="toggleAddContactsForm">
                                        <div class="main__list__item" wire:key="0">
                                            <div class="main__list__item__infos">
                                                <h3>{!! __('button.add_contacts') !!}</h3>
                                            </div>
                                        </div>
                                    </li>
                                @endif
                                @foreach ($this->contacts as $contact)
                                    <li>
                                        <div class="main__list__item" wire:key="{!! $contact->id !!}"
                                            wire:click.prevent="toggleEditContactProjectForm('{!! $contact->slug !!}')">
                                            <div class="main__list__item__infos">
                                                <h3>{!! $contact->firstname . ' ' . $contact->lastname !!}</h3>
                                                <div class="mt-2">
                                                    <p>{!! $contact->email .
                                                        ' - ' .
                                                        __(
                                                            'title.' .
                                                                $contact->attendances()->whereJiriId($jiri->id)->first()->role,
                                                        ) !!}</p>
                                                </div>
                                                @if ($contact->jiri_projects)
                                                    <div class="main__list__item__projects mt-2">
                                                        @foreach ($contact->jiri_projects as $jiriProject)
                                                            @if (
                                                                $jiriProject->projects->where('contact_id', $contact->id)->first() &&
                                                                    $jiriProject->projects->where('contact_id', $contact->id)->first()->urls)
                                                                <button class="form__btn btn" type="button">
                                                                    <a><span>{!! $jiriProject->name !!}</span></a>
                                                                </button>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="main__list__item__picture">
                                                <img loading="lazy" width="100" height="100"
                                                    src="{!! asset($contact->image->image_url) ?? '' !!}" alt="{!! __('button.image_of') . $contact->firstname . ' ' . $contact->lastname !!}">
                                            </div>
                                        </div>
                                        @if ($editContactMode)
                                            <span class="exit"
                                                wire:click.prevent="removeContactFromJiri('{!! $contact->slug !!}')">X</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <ul class="jiri__attendances__list-edit">
                                <li class="no_reduce" wire:click.prevent="toggleAddContactsForm">
                                    <div class="main__list__item" wire:key="0">
                                        <div class="main__list__item__infos">
                                            <h3>{!! __('button.add_contacts') !!}</h3>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        @endif
                    </div>
                </form>
                <form class="form jiri__modal__form jiri__projects__form border-container">
                    <div class="modal__form__title">
                        <div class="flex-inline-sb">
                            <span class="form__title">{!! __('title.projects') !!}</span>
                            <div>
                                <span
                                    class="jiri__projects__form__weighting{!! $this->projects->sum('weighting') != 1 ? ' red' : ' green' !!}">{!! __('title.total_weighting', ['weighting' => $this->projects->sum('weighting')]) !!}</span>
                            </div>
                        </div>
                        <div>
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
                                            <h3>{!! __('input.name') !!}</h3>
                                            <div>
                                                <span wire:click.prevent="setProjectSort('name', 'asc')">&uarr;</span>
                                                <span wire:click.prevent="setProjectSort('name', 'desc')">&darr;</span>
                                            </div>
                                        </div>
                                        <div class="filter-modal__sort">
                                            <h3>{!! __('input.creation_date') !!}</h3>
                                            <div>
                                                <span
                                                    wire:click.prevent="setProjectSort('created_at', 'asc')">&uarr;</span>
                                                <span
                                                    wire:click.prevent="setProjectSort('created_at', 'desc')">&darr;</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if ($jiri->status !== 'started')
                                    <div class="form__btn btn">
                                        <button class="form__btn btn" type="button"
                                            wire:click.prevent="toggleEditProjectMode">
                                            <a><span>{!! __('button.edit') !!}
                                                </span></a>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="modal__form__list__container">
                        <ul class="jiri__projects__list{!! $editProjectMode ? '-edit' : '' !!}">
                            @if ($this->projects->isNotEmpty())
                                @foreach ($this->projects as $project)
                                    <li wire:click.prevent="toggleShowProjectForm('{!! $project->slug !!}')"
                                        wire:key="{!! $project->id !!}">
                                        <h3>{!! $project->name !!}</h3>
                                        @if ($editProjectMode)
                                            <span
                                                wire:click.prevent.stop="removeProjectFormJiri('{!! $project->slug !!}')"
                                                class="exit">X</span>
                                        @endif
                                    </li>
                                @endforeach
                                @if ($editProjectMode)
                                    <li class="long" wire:key="0" wire:click.prevent="toggleShowProjectForm">
                                        <h3>{!! __('button.add_project') !!}</h3>
                                    </li>
                                @endif
                            @else
                                <li class="long" wire:key="0" wire:click.prevent="toggleShowProjectForm">
                                    <h3>{!! __('button.add_project') !!}</h3>
                                </li>
                            @endif

                        </ul>
                    </div>
                </form>
                <div class="modal__footer"></div>
            </div>
            @if ($showAddContactsForm)
                <div class="modal">
                    <div class="modal__over tall__modal__over">
                        <div class="modal-title-exit-row">
                            <h3 class="modal__title">{!! __('title.add_contact') !!}
                            </h3>
                            <span wire:click.prevent="toggleAddContactsForm" class="modal__exit">X</span>
                        </div>
                        <div class="modal__over__form">
                            <form class="form jiri__addContact__form">
                                <div class="flex-inline-sb">
                                    <div class="search{!! $addContactSearch ? '-locked' : '' !!}">
                                        <x-input :type="'search'" :labelFor="'contact'" :name="'search'"
                                            :wireModel="'addContactSearch'" :required=false />
                                    </div>
                                    <div class="form__input form__switch">
                                        <span class="switch__text">{!! __('title.evaluator') !!}
                                        </span>
                                        <label class="switch">
                                            <input type="checkbox" wire:model.live="switchAddContactType">
                                            <span class="slider"></span>
                                        </label>
                                        <span class="switch__text">{!! __('title.student') !!}</span>
                                    </div>
                                </div>
                                <ul class="second-list list-500-hidden">
                                    @foreach ($this->add_contact_list as $contact)
                                        <li class="home__students-attendances__left__item second-list__item relative"
                                            wire:key="{!! $contact->id !!}"
                                            wire:click.prevent="addNewContactToJiri('{!! $contact->slug !!}')">
                                            <div class="second-list__item__infos">
                                                <h3>{!! $contact->firstname . ' ' . $contact->lastname !!}</h3>
                                            </div>
                                            <div class="second-list__item__avatar avatar">
                                                <img width="100" height="100" src="{!! asset($contact->image->image_url) . '?' . $contact->image->updated_at->format('U') ?? '' !!}"
                                                    alt="{!! __('button.image_of') . $contact->firstname . ' ' . $contact->lastname !!}">
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
            @if ($showProjectForm)
                <div class="modal">
                    <div class="modal__over">
                        <div class="flex-inline-sb">
                            <h3 class="modal__title">{!! $selectedProject->name ?? __('title.new_project') !!}</h3>
                            <div class="modal-title-exit">
                                <span wire:click.prevent="toggleShowProjectForm" class="modal__exit">X</span>
                            </div>
                        </div>
                        <div class="modal__over__form">
                            <form class="form">
                                <x-input :type="'text'" :labelFor="'newProject'" :name="'newProjectName'" :wireModel="'newProjectName'"
                                    :required=true />
                                <x-input :type="'number'" :labelFor="'newProject'" :name="'newProjectWeighting'" :wireModel="'newProjectWeighting'"
                                    :required=true />
                                <div class="input">
                                    <label for="project_description">{!! __('input.description') !!}</label>
                                    <textarea maxlength="255" wire:model.blur="newProjectDescription" name="project_description"
                                        id="project_description" cols="30" rows="5"></textarea>
                                </div>
                                @if ($jiri->status !== 'started')
                                    <div class="form__btn btn">
                                        <button class="form__btn btn" type="button"
                                            wire:click.prevent="updateOrCreateProject{!! $selectedProject ? "('$selectedProject->slug')" : '' !!}">
                                            <a><span>{!! $selectedProject ? __('button.update_project') : __('button.add_project') !!}
                                                </span></a>
                                        </button>
                                    </div>
                                @endif
                            </form>
                        </div>
                    </div>
            @endif
            @if ($showContactEditForm && $selectedContact)
                <div class="modal">
                    <div class="modal__over">
                        <div class="flex-inline-sb">
                            <h3 class="modal__title">{!! $selectedContact->firstname . ' ' . $selectedContact->lastname !!}</h3>
                            <div class="modal-title-exit">
                                <span wire:click.prevent="toggleEditContactProjectForm" class="modal__exit">X</span>
                            </div>
                        </div>
                        <div class="fz-20 flex-inline-sb">
                            <div>
                                <p>{!! $selectedContact->email !!}</p>
                            </div>
                            <div class="avatar">
                                <img width="100" height="100" src="{!! asset($selectedContact->image->image_url) ?? '' !!}"
                                    alt="{!! __('button.image_of') . $selectedContact->firstname . ' ' . $selectedContact->lastname !!}">
                            </div>
                        </div>
                        @if ($selectedContactRole === 'student')
                            <div class="modal__over__form">
                                <form class="form update__projects__form">
                                    <h3>{!! __('title.projects_links') !!}
                                    </h3>
                                    @if ($jiriProjects)
                                        @foreach ($jiriProjects as $projectName => $urls)
                                            <div class="update__projects__form__container">
                                                <p class="update__projects__form__title">{!! $projectName !!}</p>
                                                @if ($jiriProjects[$projectName] === '[]')
                                                    <p>{!! __('title.no_projects') !!}</p>
                                                @endif
                                                @php
                                                    $usedTypes = collect(json_decode($urls, true))
                                                        ->pluck('type')
                                                        ->filter()
                                                        ->unique()
                                                        ->all();
                                                @endphp
                                                @foreach (json_decode($urls, true) as $index => $url)
                                                    @if (isset($jiriProjectsErrors[$projectName][$index]))
                                                        <span
                                                            class="error">{{ $jiriProjectsErrors[$projectName][$index] }}</span>
                                                    @endif
                                                    <div class="input-select">
                                                        <input type="text" placeholder="{!! __('input.link') !!}"
                                                            wire:change="updateLink('{{ $projectName }}', {{ $index }}, $event.target.value)"
                                                            value="{{ $url['link'] }}">
                                                        <select
                                                            wire:change="updateType('{{ $projectName }}', {{ $index }}, $event.target.value)">
                                                            <option class="shrink__option" value="null">Type &darr;
                                                            </option>
                                                            @php
                                                                $types = ['design', 'github', 'site'];
                                                            @endphp
                                                            @foreach ($types as $type)
                                                                @if ($url['type'] == $type || !in_array($type, $usedTypes))
                                                                    <option value="{{ $type }}"
                                                                        @if ($url['type'] == $type) selected @endif>
                                                                        {!! ucfirst($type) !!}
                                                                    </option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                        <button type="button"
                                                            wire:click.prevent="removeLinkFromProject('{{ $projectName }}', {{ $index }})">-</button>
                                                    </div>
                                                @endforeach
                                                <button type="button" class="plus__btn"
                                                    wire:click.prevent="addLinkToProject('{{ $projectName }}')">+</button>
                                            </div>
                                        @endforeach
                                    @else
                                        <p class="mb-2">{!! __('title.no_projects') !!}</p>
                                    @endif
                                    <div class="form__btn btn">
                                        <button class="form__btn btn" type="button" {!! !empty(array_filter($jiriProjectsErrors)) ? 'disabled' : '' !!}
                                            wire:click.prevent="saveJiriProjects">
                                            <a><span>{!! __('button.save_projects') !!}
                                                </span></a>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
            @if ($showStartJiriForm)
                <div class="modal">
                    <div class="modal__over">
                        <div class="flex-inline-sb">
                            <h3 class="modal__title"><b>{!! __('title.start_jiri', ['jiri' => $jiri->name]) !!}</b></h3>
                            <div class="modal-title-exit">
                                <span wire:click.prevent="toggleStartJiriModal" class="modal__exit">X</span>
                            </div>
                        </div>
                        <div class="modal__over__form">
                            <form class="form start__jiri__form" wire:submit.prevent="startJiri">
                                <p class="bold"><b>Voulez-vous vraiment démarrer le jiri : {!! $jiri->name !!}
                                        ?</b></p>
                                @if ($overOrUnderWeighting && $overOrUnderWeighting > 1)
                                    <div class="red">
                                        <p>La pondération totale des projets est supérieure à 1.</p>
                                        <p>Pondération totale : {!! $overOrUnderWeighting !!}</p>
                                    </div>
                                @elseif ($overOrUnderWeighting && $overOrUnderWeighting < 1)
                                    <div class="red">
                                        <p>La pondération totale des projets est inférieure à 1.</p>
                                        <p>Pondération totale : {!! $overOrUnderWeighting !!}</p>
                                    </div>
                                @endif
                                @if ($studentsWithNoProjects)
                                    <p>Certains étudiants n’ont pas de projets renseignés pour les projets
                                        suivants :</p>
                                    <div class="missing__projects">
                                        @foreach ($studentsWithNoProjects as $projectName => $students)
                                            <div class="missing__projects__project">
                                                <div>
                                                    <p>{!! $projectName !!}</p>
                                                </div>
                                                <ul>
                                                    @foreach ($students as $student)
                                                        <li>{!! $student->firstname . ' ' . $student->lastname !!}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                <div class="flex-inline-sb">
                                    <div class="delete-form__btn">
                                        <x-button :type="'button'" :translationKey="'cancel'" @click="open = false;" />
                                    </div>
                                    <div class="delete-form__btn">
                                        <x-button :type="'submit'" :translationKey="'start'" />
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
            @if ($showDeleteJiriForm)
                <div class="modal">
                    <div class="modal__over">
                        <div class="flex-inline-sb">
                            <h3 class="modal__title"><b>{!! __('title.confirm_delete_jiri', ['jiri' => $jiri->name]) !!}</b></h3>
                            <div class="modal-title-exit">
                                <span wire:click.prevent="toggleDeleteJiriModal" class="modal__exit">X</span>

                            </div>
                        </div>
                        <div class="modal__over__form">
                            <form class="form confirm__modal" wire:submit.prevent="deleteJiri">
                                <span>Voulez vous vraiment supprimer {!! $jiri->name !!} ?</span>
                                <div class="flex-inline-sb-nm">
                                    <div class="delete-form__btn">
                                        <x-button :type="'submit'" :translationKey="'confirm'" />
                                    </div>
                                    <div class="delete-form__btn">
                                        <x-button :type="'button'" :translationKey="'cancel'" @click="open = false;" />
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
            @if ($showStopJiriForm)
                <div class="modal">
                    <div class="modal__over">
                        <div class="flex-inline-sb">
                            <h3 class="modal__title"><b>{!! __('title.confirm_stop_jiri', ['jiri' => $jiri->name]) !!}</b></h3>
                            <div class="modal-title-exit">
                                <span wire:click.prevent="toggleStopJiriModal" class="modal__exit">X</span>

                            </div>
                        </div>
                        <div class="modal__over__form">
                            <form class="form confirm__modal"
                                wire:submit.prevent="stopJiri('{!! $jiri->slug !!}')">
                                <span>Voulez vous vraiment arrêter {!! $jiri->name !!} ?</span>
                                <p>Cette action rendre impossible aux évaluateurs de se connecter et rendra leurs côtes
                                    publiques.</p>
                                <div class="flex-inline-sb-nm">
                                    <div class="delete-form__btn">
                                        <x-button :type="'submit'" :translationKey="'confirm'" />
                                    </div>
                                    <div class="delete-form__btn">
                                        <x-button :type="'button'" :translationKey="'cancel'" @click="open = false;" />
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
            @if ($showFilledJiriFileModal)
                <div class="modal">
                    <div class="modal__over">
                        <div class="flex-inline-sb">
                            <h3 class="modal__title">{!! __('title.filled_jiri') !!}
                            </h3>
                            <div class="modal-title-exit">
                                <span wire:click.prevent="toggleShowFilledJiriModal" class="modal__exit">X</span>
                            </div>
                        </div>
                        <p>Les étudiants suivants auront les liens de leurs projets mis à jour suivant ce modèle :</p>
                        <ul class="studentsProjects">
                            @foreach ($filledJiriFile as $student)
                                @php
                                    $allProjectsHaveKeys = true;
                                    foreach ($student['projects'] as $project) {
                                        if (isset($project['name']) && (isset($project['github']) || isset($project['design']) || isset($project['wordpress']) || isset($project['divers']))) {
                                            $allProjectsHaveKeys = true;
                                            break;
                                        } else {
                                            $allProjectsHaveKeys = false;
                                        }
                                    }
                                @endphp
                                @if (!$allProjectsHaveKeys)
                                    @continue
                                @endif
                                <li class="studentsProjects__item">
                                    <div class="s-name">
                                        <p>{!! $student['firstname'] . ' ' . $student['lastname'] !!}</p>
                                    </div>
                                    <div class="s-projects">
                                        @foreach ($student['projects'] as $project)
                                            <div class="s-projects__item">
                                                @if (isset($project['name']) &&
                                                        (isset($project['github']) ||
                                                            isset($project['design']) ||
                                                            isset($project['wordpress']) ||
                                                            isset($project['divers'])))
                                                    <p class="s-projects-title">{!! isset($project['name']) ? $project['name'] : '' !!}</p>
                                                    <ul class="s-links">
                                                        @if (isset($project['github']))
                                                            <li>Github : {!! $project['github'] !!}</li>
                                                        @endif
                                                        @if (isset($project['design']))
                                                            <li>Design : {!! $project['design'] !!}</li>
                                                        @endif
                                                        @if (isset($project['site']))
                                                            <li>Site : {!! $project['site'] !!}</li>
                                                        @endif
                                                        @if (isset($project['divers']))
                                                            <li>Divers : {!! $project['divers'] !!}</li>
                                                        @endif
                                                    </ul>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                        <div class="flex-inline-sb">
                            <button class="form__btn btn" type="button"
                                wire:click.prevent="toggleShowFilledJiriModal">
                                <a><span>{!! __('button.cancel') !!}
                                    </span></a>
                            </button>
                            <button class="form__btn btn" type="button" wire:click.prevent="updateStudentsProjects">
                                <a><span>{!! __('button.update') !!}</span></a>
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>
