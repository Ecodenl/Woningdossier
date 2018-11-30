@if(!isset($id))
    <?php $id = "1"?>
@endif

<div class="form-builder ui-sortable-handle panel panel-default" @isset($id) id="{{$id}}" @endisset>
    <div class="panel-body">
        <div class="row">
            <div class="col-sm-12">
                {{$slot}}
            </div>
        </div>
        <div class="row" id="validation-rules">
            @include('validation-options')
        </div>
    </div>
    <div class="panel-footer">
        @include('form-build-panel-footer', ['id' => $id])
    </div>
</div>

