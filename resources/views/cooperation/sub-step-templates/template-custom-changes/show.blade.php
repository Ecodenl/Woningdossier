<div class="w-full divide-y-2 divide-blue-500 divide-opacity-20 space-y-5">
    @component('cooperation.frontend.layouts.components.form-group', [
        'class' => 'form-group-heading',
        'label' => __("livewire/cooperation/frontend/tool/simple-scan/custom-changes.question.{$scan->short}.label"),
        'withInputSource' => false,
    ])
        @slot('modalBodySlot')
            <p>
                @lang("livewire/cooperation/frontend/tool/simple-scan/custom-changes.question.{$scan->short}.help")
            </p>
        @endslot

        <livewire:cooperation.frontend.tool.simple-scan.custom-changes :scan="$scan"/>
    @endcomponent
</div>
