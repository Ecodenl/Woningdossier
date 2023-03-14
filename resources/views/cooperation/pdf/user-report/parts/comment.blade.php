<div class="row">
    <div class="col-12">
        <p>
            {{ $label }}
        </p>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <p class="comment">
            {!! nl2br(trim(htmlspecialchars($comment, ENT_QUOTES | ENT_SUBSTITUTE, null, false))) !!}
        </p>
    </div>
</div>