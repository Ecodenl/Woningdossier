<?php
    // note needs improvement bigtime

    $helpId = time();
    // get the step slug
    $slug = str_replace('/tool/', '', request()->getRequestUri());

    $currentInputSource = \App\Helpers\HoomdossierSession::getInputSource(true);
    // if not, we have to place a extra field so he can add a comment
    $currentInputSourceHasNoPlacedComment = !isset($commentsByStep[$slug][$currentInputSource->name]);
    $columnName = $columnName ?? 'comment';


    $toolUrl = explode('/', request()->getRequestUri());
    $currentSubStep = isset($toolUrl[3]) ? \App\Models\Step::where('slug', $toolUrl[3])->first() : null;

?>
@isset($commentsByStep[$currentSubStep->short])
    @foreach($commentsByStep[$currentSubStep->short] as $inputSourceName => $comment)

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
@endisset

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
