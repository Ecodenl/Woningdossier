@if(!empty($slot))
<div class="modal fade" id="{{ $id ?? ''}}">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <p>{{$slot}}</p>
            </div>
        </div>
    </div>
</div>
@endif