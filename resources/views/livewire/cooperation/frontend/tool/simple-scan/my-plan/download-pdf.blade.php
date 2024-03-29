<div class="flex w-1/2 justify-end space-x-5">
    <div @if($isFileBeingProcessed) wire:poll="checkIfFileIsProcessed" @endif>
        @if($fileStorage instanceof \App\Models\FileStorage)
            <a href="{{route('cooperation.file-storage.download', compact('cooperation', 'fileStorage'))}}" class="btn btn-purple">
                @lang('cooperation/frontend/tool.my-plan.downloads.download-report')
            </a>
        @endif
    </div>

    <button class="btn btn-purple" type="button" @if($isFileBeingProcessed) disabled="disabled" @endif wire:click="generatePdf">
        <span class="w-full mx-1 flex justify-between items-center">
            @if($isFileBeingProcessed)
                <i class="icon-md icon-ventilation-fan animate-spin-slow"></i>
                @lang('cooperation/frontend/tool.my-plan.downloads.file-is-processing')
            @else
                @lang('cooperation/frontend/tool.my-plan.downloads.create-report')
            @endif
        </span>
    </button>
</div>