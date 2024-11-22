<div class="live">
    <x-slot:title>
        {!! __('title.live_evaluation') . ' | Jiri' !!}
    </x-slot:title>
    <x-slot:page_title>
        {!! __('title.live_evaluation') !!}
    </x-slot:page_title>
    <div class="scores card" wire:poll.2500ms>
        <button class="form__btn btn" type="button" wire:click.prevent="toggleFullscreen">
            <a><span>{!! __('button.fullscreen') !!}</span></a>
        </button>
        <div class="{!! $fullscreen ? 'scores__board fullscreen' : 'scores__board' !!}">
            @if ($fullscreen)
                <div class="fullscreen__header">
                    <div class="fullscreen__header__title">{!! $jiri->name !!}</div>
                    <div class="fullscreen__header__exit exit" wire:click.prevent="toggleFullscreen">X</div>
                </div>
            @endif
            <table>
                <thead>
                    <tr>
                        <th rowspan="2" class="sticky-index">
                            <p>Évaluateur &rarr;</p>
                            <p>Étudiant &darr;</p>
                        </th>
                        @foreach ($this->jiriOngoingEvaluations['evaluators'] as $evaluator)
                            <th class="evaluator" data-evaluator="{!! $evaluator->firstname . ' ' . $evaluator->lastname !!}">
                                <p>{!! $evaluator->firstname !!}</p>
                                <p>{!! $evaluator->lastname !!}</p>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($this->jiriOngoingEvaluations['students'] as $student)
                        <tr>
                            <td class="student sticky-students"
                                data-student="{!! $student->firstname !!} {!! $student->lastname !!}">
                                <p>{!! $student->firstname !!}</p>
                                <p>{!! $student->lastname !!}</p>
                            </td>
                            @foreach ($this->jiriOngoingEvaluations['evaluators'] as $evaluator)
                                <td class="score border" data-evaluator="{!! $evaluator->firstname . ' ' . $evaluator->lastname !!}"
                                    data-student="{!! $student->firstname . ' ' . $student->lastname !!}">
                                    <select class="form-control" name="status" id="status"
                                        wire:change="updateEvaluationStatus('{!! $student->attendances->first()->id !!}', '{!! $evaluator->attendances->first()->id !!}', $event.target.value)">
                                        <option value="to_present" {!! $this->jiriOngoingEvaluations['ongoingEvaluations']->where('student_attendance_id', $student->attendances->first()->id)->where('evaluator_attendance_id', $evaluator->attendances->first()->id)->first()->status == 'to_present'
                                            ? 'selected'
                                            : '' !!}>❌
                                        </option>
                                        <option value="ongoing" {!! $this->jiriOngoingEvaluations['ongoingEvaluations']->where('student_attendance_id', $student->attendances->first()->id)->where('evaluator_attendance_id', $evaluator->attendances->first()->id)->first()->status == 'ongoing'
                                            ? 'selected'
                                            : '' !!}>⏳
                                        </option>
                                        <option value="presented" {!! $this->jiriOngoingEvaluations['ongoingEvaluations']->where('student_attendance_id', $student->attendances->first()->id)->where('evaluator_attendance_id', $evaluator->attendances->first()->id)->first()->status == 'presented'
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

<script>
    document.addEventListener('livewire:navigated', function() {
        const selects = document.querySelectorAll('.form-control');

        selects.forEach(function(select) {
            select.addEventListener('focus', function() {
                this.children[0].textContent = '❌ - Doit présenter';
                this.children[1].textContent = '⏳ - En cours';
                this.children[2].textContent = '✅ - A présenté';
            });

            select.addEventListener('blur', function() {
                this.children[0].textContent = '❌';
                this.children[1].textContent = '⏳';
                this.children[2].textContent = '✅';
            });
        });
    })
</script>
