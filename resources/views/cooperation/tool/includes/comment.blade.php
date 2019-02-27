<?php
    /** @var $inputFromAllInputSources \Illuminate\Database\Eloquent\Collection */
    $inputFromAllInputSources = $collection;

    // get the answer for all the current input source, except for the current input source.
    $answersFromInputSourceExceptCurrent = $inputFromAllInputSources->where('input_source_id', '!=', \App\Helpers\HoomdossierSession::getInputSource());
?>

@foreach($answersFromInputSourceExceptCurrent as $i => $answerFromInputSourceExceptCurrent)
    <?php
        // check if the commentColumn is dotted, ifso use array get.
        if (false !== strpos($commentColumn, '.')) {
            $comment = array_get($answerFromInputSourceExceptCurrent, $commentColumn);
        } else {
            $comment = $answerFromInputSourceExceptCurrent->$commentColumn;
        }
        $inputSourceName = $answerFromInputSourceExceptCurrent->inputSource->name;
        // generate a id for the help block
        $helpId = mt_rand(20, 100).$inputSourceName.$i;
    ?>
    @if(!empty($comment))
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group add-space">

                    <label for="" class=" control-label">
                        <i data-toggle="modal" data-target="#{{$helpId}}" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>
                        {{\App\Helpers\Translation::translate($translation['title'])}} ({{$inputSourceName}})
                    </label>

                    <textarea disabled="disabled" class="disabled form-control">{{$comment}}</textarea>

                    @component('cooperation.tool.components.help-modal')
                        {{\App\Helpers\Translation::translate($translation['help'])}}
                    @endcomponent
                </div>
            </div>
        </div>
    @endif
@endforeach