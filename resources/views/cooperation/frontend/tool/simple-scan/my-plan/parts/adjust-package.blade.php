{{--
    A new SmartTwin calculation for a woonplan that is SmartTwin's, behind the warning that it resets
    the columns. Offered under the woonplan's results ("Maatregelpakket aanpassen") and in the coach
    notice above the quick scan ("Woonplan aanpassen"); whoever includes it decides whether the
    hand-off is offered (SmartTwinHandOff).

    $id     The modal's id, for opening it from elsewhere.
    $label  The button's text, and the modal's header.
    $scan   The scan the hand-off is for.
    $class  On the wrapper; $buttonClass on the button.
--}}
<div x-data="modal()" class="{{ $class ?? '' }}">
    <button type="button" class="btn btn-green {{ $buttonClass ?? '' }}" x-on:click="toggle()">
        {{ $label }}
    </button>
    @component('cooperation.frontend.layouts.components.modal', [
        'header' => $label,
        'id' => $id,
    ])
        <p class="mb-4">@lang('cooperation/frontend/tool.my-plan.package-results.adjust-warning')</p>
        {{-- New tab, as the hand-off on the woonplan page does: the woonplan stays open behind SmartTwin. --}}
        <form method="POST" target="_blank" rel="noopener"
              action="{{ route('cooperation.frontend.tool.simple-scan.my-plan.smarttwin', ['cooperation' => $cooperation, 'scan' => $scan]) }}"
              class="flex justify-end space-x-3">
            @csrf
            <button type="button" class="btn btn-outline-green" x-on:click="close()">
                @lang('cooperation/frontend/tool.my-plan.package-results.adjust-cancel')
            </button>
            {{-- close() cancels the click it runs in while the modal is open, which would stop the
                 submit; so close only once the click (and with it the submit) is done. --}}
            <button type="submit" class="btn btn-green" x-on:click="setTimeout(() => close())">
                @lang('cooperation/frontend/tool.my-plan.package-results.adjust-confirm')
            </button>
        </form>
    @endcomponent
</div>
