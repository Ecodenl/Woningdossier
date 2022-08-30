<div>
    @foreach($failedValidationForSubSteps as $failedValidationForSubStep)
        <h1>Failed {{$failedValidationForSubStep}}</h1>
    @endforeach
    <div x-data="{ active: '{{$step->subSteps->first()->slug}}'}">
        <div class="hidden sm:block">
            <nav class="flex space-x-4" aria-label="Tabs">
                <!-- Current: "bg-indigo-100 text-indigo-700", Default: "text-green-500 hover:text-green-700" -->
                @foreach($step->subSteps as $subStep)
                    <a x-on:click="active = '{{$subStep->slug}}'" href="#" class="no-underline rounded-md p-2 bg-green text-white hover:bg-gray ">
                        {{$subStep->name}}
                    </a>
                @endforeach
            </nav>
        </div>


        @foreach($step->subSteps as $subStep)
             <div x-show="active == '{{$subStep->slug}}'">
                @livewire('cooperation.frontend.tool.expert-scan.sub-steppable', ['step' => $step, 'subStep' => $subStep]))
             </div>
         @endforeach
    </div>

    <button wire:click="$emitTo('cooperation.frontend.tool.expert-scan.sub-steppable', 'save')" class="float-right btn btn-purple">
        @lang('default.buttons.save')
    </button>
</div>
