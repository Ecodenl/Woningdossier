<div class="alert alert-{{isset($type) ? $type : "success"}} alert-dismissible {{isset($hide) && $hide == true ? 'hide' : ''}}" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    {{$slot}}
</div>