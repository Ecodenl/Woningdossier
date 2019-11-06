<?php

    $helpId = time();
    $currentInputSource = \App\Helpers\HoomdossierSession::getInputSource(true);

    // obtain the comments for the current step, when its a substep, the comment will be stored in the substep
    // else get it from the main step
    $commentsForCurrentStep = $commentsByStep[$currentSubStep->short ?? $currentStep->short] ?? [];

    $columnName = $columnName ?? 'step_comments[comment]';
    if (isset($short)) {
        $columnName = 'step_comments[comment]['.$short.']';
        $currentInputSourceHasNoPlacedComment = !isset($commentsForCurrentStep[$short][$currentInputSource->name]);
    } else {
        $currentInputSourceHasNoPlacedComment = !isset($commentsForCurrentStep[$currentInputSource->name]);
    }
?>
@if(!empty($commentsForCurrentStep))
@foreach($commentsForCurrentStep as $inputSourceName => $comment)

        {{--a nice uitzondering op de regel for only one case--}}
        @if(is_array($comment))
            <?php $comment = $comment[$short]; ?>
        @endif
        {{-- The column can be a category, this will be the case when the comment is stored under a catergory --}}
        @if(!empty($comment))
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group add-space">

                    <label for="" class=" control-label">
                        <i data-toggle="modal" data-target="#{{$helpId}}" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>
                        {{\App\Helpers\Translation::translate($translation.'.title')}} @if($currentInputSource->name != $inputSourceName)({{$inputSourceName}}) @endif
                    </label>

                    @if($inputSourceName === $currentInputSource->name)
                        <textarea name="{{$columnName}}" class="form-control">{{old($columnName, $comment)}}</textarea>
                    @else
                        <textarea disabled="disabled" class="disabled form-control">{{$comment}}</textarea>
                    @endif

                    @component('cooperation.tool.components.help-modal')
                        {{\App\Helpers\Translation::translate($translation.'.help')}}
                    @endcomponent
                </div>
            </div>
        </div>
        @endif
    @endforeach
@endif
@if($currentInputSourceHasNoPlacedComment)
<div class="row">
    <div class="col-sm-12">
        <div class="form-group add-space">

            <label for="" class=" control-label">
                <i data-toggle="modal" data-target="#{{$helpId}}" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>
                {{\App\Helpers\Translation::translate($translation.'.title')}}
            </label>

            <textarea name="{{$columnName}}" class="form-control">{{old($columnName)}}</textarea>

            @component('cooperation.tool.components.help-modal')
                {{\App\Helpers\Translation::translate($translation.'.help')}}
            @endcomponent
        </div>
    </div>
</div>
@endif
