<button class="float-right btn btn-purple"
        @if(RouteLogic::inQuestionnaire(Route::currentRouteName()))
        x-on:click="$el.setAttribute('disabled', true); document.querySelector('#questionnaire-form-{{$questionnaire->id}}').submit();"
        @else
        wire:click="$emitTo('cooperation.frontend.tool.expert-scan.form', 'save')"
        x-on:click="$el.setAttribute('disabled', true);"
        @endif
        x-on:validation-failed.window="$el.removeAttribute('disabled');">
    @lang('default.buttons.save')
</button>