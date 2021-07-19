<div class="modal-container" x-show="open" x-on:keydown.escape.window="close()">
    <div class="modal" x-ref="modal" x-on:click.outside="close()">
        <div class="modal-header">
            <i class="icon-sm icon-info mr-3"></i>
            <h6 class="heading-6">
                @lang('cooperation/tool/shared.modals.info')
            </h6>
            <div class="modal-close" x-on:click="close()">
                <i class="icon-md icon-close-circle-light"></i>
            </div>
        </div>
        <div class="modal-body">
            {{ $slot }}
        </div>
    </div>
</div>