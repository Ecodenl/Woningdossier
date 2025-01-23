<div>
    <div class="flex flex-wrap w-full mb-4" wire:poll>
        <button type="button" wire:click="generate"
                @if($fileType->isBeingProcessed()) disabled @endif
                class="@if($fileType->isBeingProcessed()) disabled @endif btn btn-blue flex items-center mb-2 mr-2">
            Genereer nieuwe voorbeeldwoning CSV
            @if($fileType->isBeingProcessed())
                <i class="icon-sm icon-ventilation-fan animate-spin-slow ml-2"></i>
            @endif
        </button>

        @if($fileStorage instanceof \App\Models\FileStorage && ! $fileStorage->isBeingProcessed())
            <a href="{{route('cooperation.file-storage.download', ['cooperation' => $cooperation, 'fileStorage' => $fileStorage])}}"
               class="btn btn-purple mb-2">
                Download bestaande voorbeeldwoning CSV ({{$fileStorage->created_at->format('Y-m-d H:i')}})
            </a>
        @endif
    </div>
</div>
