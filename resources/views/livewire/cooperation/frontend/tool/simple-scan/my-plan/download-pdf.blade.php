<div class="w-full flex pt-5 pb-5">
    <div class="flex w-1/3">
        @can('viewAny', [\App\Models\Media::class, $inputSource, $user->building])
            <a href="{{ route('cooperation.frontend.tool.quick-scan.my-plan.media', ['cooperation' => $cooperation]) }}"
               class="btn btn-outline-green">
                @lang('cooperation/frontend/tool.my-plan.uploader.view')
            </a>
        @endcan
    </div>
    <div class="flex w-2/3 justify-end space-x-5">
        <div wire:poll="checkIfFileIsProcessed">
            @if($fileStorage instanceof \App\Models\FileStorage)
                <a href="{{route('cooperation.file-storage.download', compact('cooperation', 'fileStorage'))}}" class="btn btn-purple">
                    @lang('cooperation/frontend/tool.my-plan.download-report')
                </a>
            @endif
        </div>

        <button class="btn btn-purple" type="button" @if($isFileBeingProcessed) disabled="disabled" @endif wire:click="generatePdf">
            <span class="w-full mx-1 flex justify-between items-center">
                @if($isFileBeingProcessed)
                    <i class="icon-md icon-ventilation-fan animate-spin-slow"></i>
                    @lang('cooperation/frontend/tool.my-plan.file-is-processing')
                @else
                    @lang('cooperation/frontend/tool.my-plan.create-report')
                @endif
            </span>
        </button>
    </div>
</div>
