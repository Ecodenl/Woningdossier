<div class="flex items-center justify-between w-full bg-blue-100 border-b-1 h-16 px-5 xl:px-20 relative z-30">
    @foreach($steps as $step)
    <div class="flex items-center h-full">
        @if($building->hasCompleted($step, \App\Helpers\HoomdossierSession::getInputSource(true)))
        <i class="icon-sm icon-check-circle-dark mr-1 border-purple"></i>
        @elseif($currentStep->short == $step->short)
        <i class="icon-sm bg-purple bg-opacity-25 rounded-full border border-solid border-purple mr-1"></i>
        @else
        <i class="icon-sm bg-transparent rounded-full border border-solid border-blue mr-1"></i>
        @endif
        <span class="text-{{$currentStep->short == $step->short ? 'purple' : 'blue'}}">{{$step->name}}</span>
    </div>
    @if(!$loop->last)
        <div class="step-divider-line"></div>
    @endif
    @endforeach
    <div class="border border-blue-500 border-opacity-50 h-1/2"></div>
    <div class="flex items-center justify-start h-full">
        <i class="icon-sm icon-house-dark mr-1"></i>
        <span class="text-blue">Woonplan</span>
    </div>
</div>
{{-- Progress bar --}}
<div class="w-full bg-gray h-2">
    {{-- Define style-width based on step progress divided by total steps --}}
    <div class="h-full bg-purple" style="width: 30%"></div>
</div>