<div class="w-full divide-y-2 divide-blue-500 divide-opacity-20 space-y-{{$toolQuestions->count() > 1 ? 10 : 5}} ">

    @foreach($subStep->subSteppables as $subSteppablePivot)
        @switch($subSteppablePivot->sub_steppable_type)
            @case(\App\Models\ToolQuestion::class)
                @include('cooperation.frontend.tool.simple-scan.parts.tool-question')
                @break
            @case(\App\Models\ToolLabel::class)
                @include('cooperation.frontend.tool.simple-scan.parts.tool-label')
                @break
        @endswitch
    @endforeach
</div>
