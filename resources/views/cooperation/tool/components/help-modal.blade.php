<div class="modal fade" id="{{isset($id) ? $id :''}}">
    <div class="modal-dialog" >
        <div class="modal-content">
            <div class="modal-body">
                {{$slot}}
            </div>
        </div>
    </div>
</div>