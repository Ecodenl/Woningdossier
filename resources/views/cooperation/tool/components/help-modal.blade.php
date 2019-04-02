@if(!empty($slot))
<div class="modal fade" id="{{ $id ?? ''}}">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <?php
                /**
                 * @var \Illuminate\Support\HtmlString $slot
                 */
                    $htmlString = $slot->toHtml();
//                    echo  $htmlString;
                ?>
                {!! $htmlString !!}

            </div>
        </div>
    </div>
</div>
@endif