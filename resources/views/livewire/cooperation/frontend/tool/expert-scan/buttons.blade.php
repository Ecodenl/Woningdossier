<button class="float-right btn btn-purple"
        wire:click="$emitTo('cooperation.frontend.tool.expert-scan.sub-steppable', 'save')"
        x-on:click="$dispatch('save-clicked')"
        x-on:save-clicked.window="$el.setAttribute('disabled', true);"
        x-on:validation-failed.window="$el.removeAttribute('disabled');">
    @lang('default.buttons.save')
</button>