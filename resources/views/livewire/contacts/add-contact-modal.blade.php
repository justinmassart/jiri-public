<div class="modal__content">
    <div class="modal__content__container">
        <div class="modal__header">
            <div class="flex-inline-sb">
                @if ($formStep !== 1)
                    <div class="back">
                        <h3 class="modal__title{!! $formStep !== 1 ? ' ml-2' : '' !!}">{!! __('title.add_contact') !!}
                            </h2>
                            <span wire:click.prevent="previousStep">&larr;</span>

                    </div>
                    <div class="modal-title-exit">
                        <span wire:click.prevent="$parent.toggleAddContactModal()" class="modal__exit">X</span>
                    </div>
                @else
                    <h3 class="modal__title">{!! __('title.add_contact') !!}</h3>
                    <div class="modal-title-exit">
                        <span wire:click.prevent="$parent.toggleAddContactModal()" class="modal__exit">X</span>
                    </div>
                @endif
            </div>
            @if ($formStep === 1)
                <div class="contact__import">
                    <button class="btn" type="button" onclick="document.getElementById('importContacts').click();">
                        <a><span>{!! __('button.import_contacts') !!}</span></a>
                    </button>
                    <input wire:model.live="contactFile" type="file" id="importContacts" style="display: none;"
                        onchange="handleFileImport(this)">
                </div>
            @endif
        </div>
        @if ($formStep === 1)
            <form class="form">
                <div>
                    <div class="form__input form__switch">
                        <span class="switch__text">{!! __('title.evaluator') !!}
                        </span>
                        <label class="switch">
                            <input type="checkbox" wire:model.live="switchRole">
                            <span class="slider"></span>
                        </label>
                        <span class="switch__text">{!! __('title.student') !!}</span>
                    </div>
                    <x-input :type="'text'" :labelFor="'contact'" :name="'firstname'" :wireModel="'firstname'" :required=true />
                    <x-input :type="'text'" :labelFor="'contact'" :name="'lastname'" :wireModel="'lastname'" :required=true />
                    <x-input :type="'email'" :labelFor="'contact'" :name="'email'" :wireModel="'email'" :required=true />
                    <div class="input__picture relative" @click="$refs.fileInput.click()">
                        <input wire:model.live="picture" type="file" class="hidden" x-ref="fileInput">
                        <label>Avatar</label>
                        <div>
                            <svg version="1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48"
                                enable-background="new 0 0 48 48" width="100px"
                                style="border: 1px solid black; border-radius: 1rem">
                                <rect width="100%" height="100%" fill="white" />
                                <polygon fill="#9AC9E3" points="20,16 9,32 31,32" />
                                <polygon fill="#B3DDF5" points="31,22 23,32 39,32" />
                                <g fill="#0074c2">
                                    <rect x="36" y="32" width="4" height="12" />
                                    <rect x="32" y="36" width="12" height="4" />
                                </g>
                            </svg>
                            @if ($picture)
                                <div>
                                    <img src="{{ $picture->temporaryUrl() }}" width="200">
                                </div>
                            @endif
                        </div>
                    </div>
                    @if ($picture)
                        <button class="btn-red" type="button" wire:click.prevent="removePicture">
                            <a><span>{!! __('button.delete') !!}</span></a>
                        </button>
                    @endif
                    <div class="form__input input-select-long">
                        <label for="contact-jiri">{!! __('input.assign_to_jury') !!}
                        </label>
                        <select @error('jiri')class="error" @enderror wire:model.live="jiri" name="contact-jiri"
                            id="contact-jiri" style="min-width: 20rem">
                            <option value="0">{!! __('input.assign_to_jury_placeholder') !!}
                            </option>
                            @foreach ($jiris as $j)
                                <option value="{{ $j->id }}">{{ $j->name }}</option>
                            @endforeach
                        </select>
                        @error('jiri')
                            <span class="modal__error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="flex-inline-sb">
                    <div class="form__btn btn">
                        <button class="form__btn btn" type="submit" wire:click.prevent="createContact">
                            <a><span>{!! __('button.validate') !!}
                                </span></a>
                        </button>
                    </div>
                    @if ($role === 'evaluator' || ($role === 'student' && $jiri === null))
                        <div class="form__btn btn">
                            <button class="form__btn btn" type="submit"
                                wire:click.prevent="createContact(false, true)">
                                <a><span>{!! __('button.validate_and_create_new_contact') !!}
                                    </span></a>
                            </button>
                        </div>
                    @elseif ($role === 'student' && $jiri !== null)
                        <div class="form__btn btn">
                            <button class="form__btn btn" type="button" wire:click.prevent="createContact(true)">
                                <a><span>{!! __('button.validate_and_add_projects') !!}
                                    </span></a>
                            </button>
                        </div>
                    @endif
                </div>
            </form>
        @endif
        @if ($formStep === 2)
            <form class="form update__projects__form">
                <h3>{!! __('title.projects_links') !!}
                </h3>
                @foreach ($projects as $projectName => $project)
                    <div class="update__projects__form__container">
                        <p class="update__projects__form__title">{!! $projectName !!}</p>
                        @foreach ($project as $index => $projectLink)
                            <div class="input-select">
                                <input type="text" placeholder="{!! __('input.project_link_placeholder') !!}"
                                    wire:model.blur="projects.{{ $projectName }}.{{ $index }}.link">
                                <select wire:model.blur="projects.{{ $projectName }}.{{ $index }}.type">
                                    <option selected value="null">{!! __('input.type') !!}
                                    </option>
                                    <option value="design">{!! __('input.design') !!}
                                    </option>
                                    <option value="website">{!! __('input.website') !!}
                                    </option>
                                    <option value="github">{!! __('input.github') !!}
                                    </option>
                                    <option value="other">{!! __('input.other') !!}
                                    </option>
                                </select>
                                <button type="button"
                                    wire:click.prevent="removeLinkFromProject('{{ $projectName }}', {{ $index }})">-</button>
                            </div>
                        @endforeach
                        <button type="button" class="plus__btn"
                            wire:click.prevent="addLinkToProject('{{ $projectName }}')">+</button>
                    </div>
                @endforeach
                <button class="form__btn btn" type="button" wire:click.prevent="assignProjects">
                    <a><span>{!! __('button.validate') !!}
                        </span></a>
                </button>
            </form>
        @endif
        @if ($showImportedContactsModal)
            <div class="modal">
                <div class="modal__over">
                    <div class="flex-inline-sb-top">
                        <h3 class="modal__title">{!! __('title.imported_contacts') !!}
                        </h3>
                        <div class="modal-title-exit">
                            <span wire:click.prevent="hideEvaluation" class="modal__exit">X</span>
                        </div>
                    </div>
                    <div>
                        <p class="mb-2">Voici la liste des contacts qui seront importés. Vous pouvez supprimer ceux
                            que vous ne
                            voulez pas importer.</p>
                        <ul class="second-list">
                            @foreach ($importedContacts as $index => $contact)
                                <li class="second-list__item relative" wire:key="{!! $contact['firstname'] . '-' . $contact['lastname'] !!}">
                                    <div class="flex-inline-sb-nm">
                                        <h3 class="mr-2"><b>{!! $contact['firstname'] . ' ' . $contact['lastname'] !!}</b></h3>
                                        <p>{!! $contact['email'] !!}</p>
                                    </div>
                                    <div class="remove"
                                        wire:click.prevent="removeImportedContact({!! $index !!})">
                                        <span>X</span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                        <div class="flex-inline-sb-mtb">
                            <button class="btn" type="button" wire:click.prevent="createImportedContacts">
                                <a><span>{!! __('button.create_contacts') !!}
                                    </span></a>
                            </button>
                            <button class="btn-red" type="button" wire:click.prevent="hideImportedContactsModal">
                                <a><span>{!! __('button.cancel') !!}
                                    </span></a>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
