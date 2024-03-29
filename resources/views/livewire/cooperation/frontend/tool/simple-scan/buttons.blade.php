<div class="w-1/2 flex flex-wrap justify-center">
    @if(! empty($previousUrl))
        <a href="{{$previousUrl}}" class="btn btn-outline-purple flex items-center mr-1">
            <i class="icon-xs icon-arrow-left-bold-purple mr-5"></i>
            @lang('default.buttons.previous')
        </a>
    @endif

    <button type="button" x-data
            {{-- Because a questionnaire is simply saved using an old school controller, we update the action, which
            will handle redirecting us correctly to the questionnaire... TODO: Refactor this, no time for it now --}}
            @if(RouteLogic::inQuestionnaire(Route::currentRouteName()))
            x-on:click="$el.setAttribute('disabled', true); document.querySelector('#questionnaire-form-{{$questionnaire->id}}').submit();"
            @else
            wire:click="$emitTo('cooperation.frontend.tool.simple-scan.form', 'save')"
            x-on:click="$el.setAttribute('disabled', true);"
            @endif
            x-on:validation-failed.window="$el.removeAttribute('disabled');"
            class="btn btn-purple flex items-center ml-1">
        @lang('default.buttons.next')
        <i class="icon-xs icon-arrow-right-bold-purple ml-5"></i>
    </button>
</div>
