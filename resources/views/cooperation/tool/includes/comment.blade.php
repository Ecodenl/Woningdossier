<?php
    $helpId = time();
    // get the step slug
    $slug = str_replace('/tool/', '', request()->getRequestUri());
?>
@foreach($commentsByStep[$slug] as $inputSourceName => $commentsCategorizedUnderColumn)
    {{-- The column can be a category, this will be the case when the comment is stored under a catergory --}}
    @foreach($commentsCategorizedUnderColumn as $columnOrCategory => $comment)
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group add-space">
                    @if(is_array($comment))
                        @foreach($comment as $column => $c)

                            <label for="" class=" control-label">
                                <i data-toggle="modal" data-target="#{{$helpId}}" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>
                                {{\App\Helpers\Translation::translate($translation.'.title')}} {{$inputSourceName}} ({{$columnOrCategory}})
                            </label>

                            @if($inputSourceName === \App\Helpers\HoomdossierSession::getInputSource(true)->name)
                                <textarea name="comment" class="form-control">{{old('comment', $c)}}</textarea>
                            @else
                                <textarea disabled="disabled" class="disabled form-control">{{$c}}</textarea>
                            @endif

                            @component('cooperation.tool.components.help-modal')
                                {{\App\Helpers\Translation::translate($translation.'.help')}}
                            @endcomponent


                        @endforeach
                    @else

                        <label for="" class=" control-label">
                            <i data-toggle="modal" data-target="#{{$helpId}}" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>
                            {{\App\Helpers\Translation::translate($translation.'.title')}} ({{$inputSourceName}})
                        </label>

                        @if($inputSourceName === \App\Helpers\HoomdossierSession::getInputSource(true)->name)
                            <textarea name="comment" class="form-control">{{old('comment', $comment)}}</textarea>
                        @else
                            <textarea disabled="disabled" class="disabled form-control">{{$comment}}</textarea>
                        @endif

                        @component('cooperation.tool.components.help-modal')
                            {{\App\Helpers\Translation::translate($translation.'.help')}}
                        @endcomponent
                    @endif

                </div>
            </div>
        </div>
    @endforeach

@endforeach
