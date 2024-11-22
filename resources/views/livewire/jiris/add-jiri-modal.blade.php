<div class="modal__content" @click.away="open = false;">
    <div class="modal__content__container">
        <div class="modal__header">
            <div class="modal-title-exit">

                <div class="flex-inline-sb">
                    @if ($formStep === 1)
                        <h2 class="modal__title{!! $formStep !== 1 ? ' ml-2' : '' !!}">{!! __('title.add_jiri') !!}
                        </h2>
                    @else
                        <div class="back">
                            <h2 class="modal__title{!! $formStep !== 1 ? ' ml-2' : '' !!}">{!! __('title.add_jiri') !!}
                            </h2>
                            <span wire:click.prevent="previousStep">&larr;</span>
                        </div>
                    @endif
                    <span wire:click.prevent="$parent.toggleAddJiriModal" class="modal__exit">X</span>
                </div>
            </div>
        </div>
        @if ($formStep === 1)
            <form class="form">
                <x-input :type="'text'" :labelFor="'jiri'" :name="'name'" :wireModel="'name'" :required=true />
                <x-input :type="'datetime-local'" :labelFor="'jiri'" :name="'starts_at'" :wireModel="'startsAt'" :required=true />
                <div class="input">
                    <label for="jiri-endsAt">{!! __('input.duration') !!}</label>
                    <input type="time" id="jiri-endsAt" name="endsAt" value="{{ $endsAt }}"
                        wire:model.blur="endsAt">
                    <x-input-error :inputName="'endsAt'" />
                </div>
                <div class="flex-inline-sb">
                    <div class="form__btn btn">
                        <button class="form__btn btn" type="submit" wire:click.prevent="createJiri; open = false;"
                            {!! $name && $startsAt && $endsAt !== '00:00' ? '' : 'disabled' !!}>
                            <a><span>{!! __('button.validate') !!}
                                </span></a>
                        </button>
                    </div>
                    <div class="form__btn btn">
                        <button class="form__btn btn" type="submit" wire:click.prevent="createJiri(true);"
                            {!! $name && $startsAt && $endsAt !== '00:00' ? '' : 'disabled' !!}>
                            <a><span>{!! __('button.validate_and_add_projects') !!}
                                </span></a>
                        </button>
                    </div>
                </div>
            </form>
        @endif
        @if ($formStep === 2)
            <form class="form">
                @foreach ($projects as $index => $project)
                    <div class="jiri__add-jiri__project">
                        <div>
                            <div class="input">
                                <label for="projectName">{!! __('input.name') !!}
                                </label>
                                <input type="text" id="projectName" placeholder="{!! __('input.name_placeholder') !!}"
                                    wire:model.blur="projects.{!! $index !!}.name">
                            </div>
                            <div class="input">
                                <label for="projectWeighting">{!! __('input.weighting') !!}
                                </label>
                                <input type="number" min="0" max="1" step="0.01" id="projectWeighting"
                                    placeholder="{!! __('input.weighting_placeholder') !!}"
                                    wire:model.blur="projects.{!! $index !!}.weighting">
                            </div>
                            <div class="input">
                                <label for="project_description">{!! __('input.description') !!}</label>
                                <textarea wire:model.blur="projects.{!! $index !!}.description" name="project_description"
                                    placeholder="{!! __('input.description_placeholder') !!}" id="project_description" cols="30" rows="5"></textarea>
                            </div>
                        </div>
                        <button class="form__btn btn-red" type="submit"
                            wire:click.prevent="removeProject({!! $index !!})">
                            <a><span>{!! __('button.remove') !!}
                                </span></a>
                        </button>
                    </div>
                @endforeach
                <div class="form__btn btn mb-2">
                    <button class="form__btn btn" type="submit" wire:click.prevent="addProject">
                        <a><span>{!! __('button.add_project') !!}
                            </span></a>
                    </button>
                </div>
                <div class="flex-inline-sb">
                    <div class="form__btn btn">
                        <button class="form__btn btn" type="submit"
                            wire:click.prevent="addProjectsToJiri; open = false;">
                            <a><span>{!! __('button.validate') !!}
                                </span></a>
                        </button>
                    </div>
                    <div class="form__btn btn">
                        <button class="form__btn btn" type="submit" wire:click.prevent="addProjectsToJiri(true)">
                            <a><span>{!! __('button.validate_and_add_participants') !!}
                                </span></a>
                        </button>
                    </div>
                </div>
            </form>
        @endif
        @if ($formStep === 3)
            <form class="form add-contacts__jiri">
                <div class="container flex-inline-sb-top">
                    <div>
                        <x-input :type="'search'" :labelFor="'jiri'" :name="'evaluatorSearch'" :wireModel="'evaluatorSearch'"
                            :required=false />
                        <ul class="second-list">
                            @foreach ($this->evaluators($this->evaluatorSearch) as $contact)
                                <li class="second-list__item" wire:click="selectEvaluator('{!! $contact->slug !!}')"
                                    wire:key="{!! $contact->id !!}">
                                    <div class="second-list__item__infos">
                                        <h3>{!! $contact->firstname . ' ' . $contact->lastname !!}</h3>
                                    </div>
                                    <div class="second-list__item__avatar avatar-small">
                                        <img width="75" height="75" src="{!! asset($contact->image->image_url) . '?' . $contact->image->updated_at->format('U') ?? '' !!}"
                                            alt="{!! __('button.image_of') . $contact->firstname . ' ' . $contact->lastname !!}">
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div>
                        <h3>{!! __('title.selected_evaluators') !!}
                        </h3>
                        <ul class="list-500-hidden second-list">
                            @foreach ($selectedEvaluators as $evaluator)
                                <li class="second-list__item second-list__item-remove"
                                    wire:click.prevent="removeEvaluator('{!! $evaluator->slug !!}')"
                                    wire:key="{!! $evaluator->id !!}">
                                    <div class="second-list__item__infos">
                                        <h3>{!! $evaluator->firstname . ' ' . $evaluator->lastname !!}</h3>
                                    </div>
                                    <div class="second-list__item__avatar avatar-small">
                                        <img width="100" height="100" src="{!! asset($evaluator->image->image_url) . '?' . $evaluator->image->updated_at->format('U') ?? '' !!}"
                                            alt="{!! __('button.image_of') . $evaluator->firstname . ' ' . $evaluator->lastname !!}">
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="container flex-inline-sb-top">
                    <div>
                        <x-input :type="'search'" :labelFor="'jiri'" :name="'evaluatorSearch'" :wireModel="'studentSearch'"
                            :required=false />
                        <ul class="second-list">
                            @foreach ($this->students($this->studentSearch) as $contact)
                                <li class="second-list__item" wire:click="selectStudent('{!! $contact->slug !!}')"
                                    wire:key="{!! $contact->id !!}">
                                    <div class="second-list__item__infos">
                                        <h3>{!! $contact->firstname . ' ' . $contact->lastname !!}</h3>
                                    </div>
                                    <div class="second-list__item__avatar avatar-small">
                                        <img width="75" height="75" src="{!! asset($contact->image->image_url) . '?' . $contact->image->updated_at->format('U') ?? '' !!}"
                                            alt="{!! __('button.image_of') . $contact->firstname . ' ' . $contact->lastname !!}">
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div>
                        <h3>{!! __('title.selected_students') !!}
                        </h3>
                        <ul class="list-500-hidden second-list">
                            @foreach ($selectedStudents as $student)
                                <li class="second-list__item second-list__item-remove"
                                    wire:click.prevent="removeStudent('{!! $student->slug !!}')"
                                    wire:key="{!! $student->id !!}">
                                    <div class="second-list__item__infos">
                                        <h3>{!! $student->firstname . ' ' . $student->lastname !!}</h3>
                                    </div>
                                    <div class="second-list__item__avatar avatar-small">
                                        <img width="100" height="100" src="{!! asset($student->image->image_url) . '?' . $student->image->updated_at->format('U') ?? '' !!}"
                                            alt="{!! __('button.image_of') . $student->firstname . ' ' . $student->lastname !!}">
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="flex-inline-sb">
                    <div class="form__btn btn">
                        <button class="form__btn btn" type="submit"
                            wire:click.prevent="addParticipantsToJiri; open = false;">
                            <a><span>{!! __('button.validate') !!}
                                </span></a>
                        </button>
                    </div>
                    <div class="form__btn btn">
                        <button class="form__btn btn" type="submit"
                            wire:click.prevent="addParticipantsToJiri; open = true">
                            <a><span>{!! __('button.validate_and_create_new_jiri') !!}
                                </span></a>
                        </button>
                    </div>
                </div>
            </form>
        @endif
    </div>
</div>

{{-- <script>
    const input = document.getElementById('jiri-endsAt');

    document.addEventListener('DOMContentLoaded', function() {
        Livewire.on('refreshComponent', () => {
            input.value = '00:00';
        });
    });

    input.addEventListener('input', function(e) {

        this.value = this.value.replace(/\D/g, '');


        if (this.value.length > 2) {
            this.value = this.value.slice(0, 2) + ':' + this.value.slice(2);
        }


        if (this.value.length > 5) {
            this.value = this.value.slice(0, 5);
        }
    });

    input.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowUp' || e.key === 'ArrowDown') {
            e.preventDefault();

            let cursorPosition = this.selectionStart;
            let value = this.value.split(':');
            let hours = parseInt(value[0]);
            let minutes = parseInt(value[1]);

            if (cursorPosition <= 2) {
                hours = e.key === 'ArrowUp' ? hours + 1 : hours - 1;
                if (hours < 0) hours = 0;
                if (hours > 100) hours = 100;
            } else {
                if (e.key === 'ArrowUp') {
                    minutes = minutes + 1 > 59 ? 0 : minutes + 1;
                } else {
                    minutes = minutes - 1 < 0 ? 59 : minutes - 1;
                }
            }

            let oldCursorPosition = this.selectionStart;

            this.value = ('0' + hours).slice(-2) + ':' + ('0' + minutes).slice(-2);

            this.selectionStart = oldCursorPosition;
            this.selectionEnd = oldCursorPosition;
        }
    });
</script> --}}
