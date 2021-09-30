<div class="w-full flex pb-5">
    <div class="flex w-full justify-end space-x-5">
        @if($fileStorage instanceof \App\Models\FileStorage)
        <a href="{{route('cooperation.file-storage.download', compact('fileStorage'))}}" class="btn btn-purple">
            @lang('cooperation/frontend/tool.my-plan.download-report')
        </a>
        @endif

        <button class="btn btn-purple" @if($isFileBeingProcessed) disabled="disabled" @endif type="button" wire:click="generatePdf">
            @lang('cooperation/frontend/tool.my-plan.create-report')
        </button>
    </div>
</div>
