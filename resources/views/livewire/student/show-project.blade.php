<div class="project">
    <x-slot:title>
        {!! session()->get('jiri')->name !!}
    </x-slot:title>
    <x-slot:page_title>
        {!! session()->get('jiri')->name .
            ' - ' .
            $student->firstname .
            ' ' .
            $student->lastname .
            ' - ' .
            $project->jiri_project->name !!}
    </x-slot:page_title>
    <div class="history">
        <span><a wire:navigate href="{!! route('evaluator.dashboard', ['jiri' => $jiri->slug]) !!}">{!! Str::slug($jiri->name) !!}</a>/<a wire:navigate
                href="{!! route('show.student', ['jiri' => $jiri->slug, 'student' => $student]) !!}">{!! $student->slug !!}</a>/<a wire:navigate
                href="{!! route('show.project', ['jiri' => $jiri->slug, 'student' => $student, 'jiri_project' => $jiri_project]) !!}">{!! $jiri_project->slug !!}</a></span>
    </div>
    <div class="project__main">
        <div class="project__main__left">
            <div class="project__main__left__links card">
                <div class="flex-inline-sb">
                    <h4>{!! __('title.links') !!}
                    </h4>
                </div>
                <ul class="projects__icons">
                    @foreach (json_decode($project->urls) ?? [] as $url)
                        <li class="project__icon">
                            <a target="_blank" href="{!! $url->link !!}">
                                {!! file_get_contents(asset('svg/' . $url->type . '.svg')) !!}
                            </a>
                            <div class="link-preview">
                                {!! $url->link !!}
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="project__main__left__otherProjects card">
                <div class="flex-inline-sb">
                    <h4>{!! __('title.other_projects', ['student' => $student->firstname . ' ' . $student->lastname]) !!}
                    </h4>
                </div>
                <ul class="other-projects">
                    @foreach ($student->projects->whereNotNull('urls') as $project)
                        <li class="other-projects__item">
                            <button class="form__btn btn" type="button">
                                <a wire:navigate
                                    href="/{!! $jiri->slug !!}/{!! $student->slug !!}/{!! $project->jiri_project->slug !!}">
                                    <span>{!! $project->jiri_project->name !!}</span>
                                </a>
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="project__main__right">
            <div class="project__main__left__score card">
                <div class="flex-inline-sb">
                    <h4>{!! __('title.score') !!}</h4>
                </div>
                <div class="score__container">
                    <input id="rangeInput" wire:model.live.debounce.1000ms="score" type="range" min="0"
                        max="20" step="0.5" class="scoreSlider{!! !$this->canEvaluateStudent ? ' opacity-25' : '' !!}"
                        {!! !$this->canEvaluateStudent ? 'disabled' : '' !!} title="{!! !$this->canEvaluateStudent ? __('input.not_allowed_score') : __('input.allowed_score') !!}">
                    <div class="score__container__score">
                        <input id="numberInput" class="scoreSlider__score{!! !$this->canEvaluateStudent ? ' opacity-25' : '' !!}" type="number"
                            min="0" max="20" step="0.01" wire:model.live.debounce.1000ms="score"
                            {!! !$this->canEvaluateStudent ? 'disabled' : '' !!} title="{!! !$this->canEvaluateStudent ? __('input.not_allowed_score') : __('input.allowed_score') !!}" /><span>/20</span>
                    </div>
                </div>
            </div>
            <div class="project__main__left__comment appreciation card">
                <div class="flex-inline-sb">
                    <h4>{!! __('title.comment') !!}</h4>
                </div>
                <textarea wire:model.live.debounce.5000ms="comment" name="comment" id="comment" cols="30" rows="10"
                    maxlength="750" placeholder="{!! __('input.appreciation') !!}" class="{!! !$this->canEvaluateStudent ? 'opacity-25' : '' !!}" {!! !$this->canEvaluateStudent ? 'disabled' : '' !!}
                    title="{!! !$this->canEvaluateStudent ? __('input.not_allowed_comment') : __('input.allowed_comment') !!}"></textarea>
            </div>
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
