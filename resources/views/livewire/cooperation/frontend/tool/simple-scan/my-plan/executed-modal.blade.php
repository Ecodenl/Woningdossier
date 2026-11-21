{{-- "Maatregel uitgevoerd": opened from a card's button or by dragging it into "In orde"; see MyPlan\Form::openExecuted(). --}}
<div x-data="modal()" x-on:open-executed-modal.window="open()" wire:key="executed-modal">
    @component('cooperation.frontend.layouts.components.modal', [
        'header' => __('cooperation/frontend/tool.my-plan.executed.title', ['measure' => $executedName]),
        'id' => 'executed-modal',
    ])
        <form wire:submit="saveExecuted" class="space-y-4">
            @component('cooperation.frontend.layouts.components.form-group', [
                'inputName' => 'executedAt',
                'id' => 'executed-at',
                'withInputSource' => false,
                'label' => __('cooperation/frontend/tool.my-plan.executed.date'),
            ])
                <input type="date" id="executed-at" class="form-input" wire:model="executedAt" max="{{ now()->format('Y-m-d') }}">
            @endcomponent

            @component('cooperation.frontend.layouts.components.form-group', [
                'inputName' => 'executedDescription',
                'id' => 'executed-description',
                'withInputSource' => false,
                'label' => __('cooperation/frontend/tool.my-plan.executed.description'),
            ])
                <textarea id="executed-description" class="form-input" rows="4" wire:model="executedDescription"></textarea>
            @endcomponent

            @component('cooperation.frontend.layouts.components.form-group', [
                'inputName' => 'executedDocuments.*',
                'id' => 'executed-documents',
                'withInputSource' => false,
                'label' => __('cooperation/frontend/tool.my-plan.executed.documents'),
            ])
                <input type="file" id="executed-documents" multiple wire:model="executedDocuments">
                <p class="text-xs text-gray-500 mt-1">@lang('cooperation/frontend/tool.my-plan.executed.documents-help')</p>
            @endcomponent

            <div class="flex justify-between items-center">
                <div>
                    @if($executedAlready)
                        <button type="button" class="btn btn-outline-red" wire:click="undoExecuted">
                            @lang('cooperation/frontend/tool.my-plan.executed.undo')
                        </button>
                    @endif
                </div>
                <div class="flex space-x-3">
                    <button type="button" class="btn btn-outline-green" x-on:click="close()">
                        @lang('cooperation/frontend/tool.my-plan.executed.cancel')
                    </button>
                    <button type="submit" class="btn btn-green" wire:loading.attr="disabled" wire:target="saveExecuted, executedDocuments">
                        @lang('cooperation/frontend/tool.my-plan.executed.save')
                    </button>
                </div>
            </div>
        </form>
    @endcomponent
</div>
