<div class="w-full divide-y-2 divide-blue-500 divide-opacity-20 space-y-5">
    @component('cooperation.frontend.layouts.components.form-group', [
        'class' => 'form-group-heading',
        'label' => __('livewire/cooperation/frontend/tool/quick-scan/custom-changes.question.label'),
        'withInputSource' => false,
    ])
        @slot('modalBodySlot')
            <p>
                @lang('livewire/cooperation/frontend/tool/quick-scan/custom-changes.question.help')
            </p>
        @endslot

        <livewire:cooperation.frontend.tool.simple-scan.custom-changes/>
    @endcomponent
</div>
