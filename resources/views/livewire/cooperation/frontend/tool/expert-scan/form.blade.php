<div>
    @foreach($step->subSteps as $subStep)
        <h1>{{$subStep->name}}</h1>
        @livewire('cooperation.frontend.tool.expert-scan.inputs', ['step' => $step, 'subStep' => $subStep])
    @endforeach
</div>
