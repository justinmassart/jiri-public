<div>
    <x-input :type="'search'" :labelFor="'{!! $labelFor !!}'" :name="'search'" :wireModel="'search'" :required=false />
    <ul>
        @foreach ($this->contacts as $contact)
            <li wire:click="select{!! ucfirst($selectFor) !!}('{!! $contact->slug !!}')">{!! $contact->firstname . ' ' . $contact->lastname !!}</li>
        @endforeach
    </ul>
    <h3>Selected Evaluators</h3>
    <ul>
        @foreach ($selectedEvaluators as $evaluator)
            <li wire:click="removeEvaluator('{!! $evaluator->slug !!}')">{!! $evaluator->firstname . ' ' . $evaluator->lastname !!}</li>
        @endforeach
    </ul>
</div>
