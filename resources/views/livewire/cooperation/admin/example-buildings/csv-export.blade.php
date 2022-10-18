<div>
    <div class="panel panel-default">
        <div class="panel-body" wire:poll>
            <button type="button" wire:click="generate"
                    @if($fileType->isBeingProcessed()) disabled="disabled" @endif
                    class="@if($fileType->isBeingProcessed()) disabled @endif btn btn-primary">
                Genereer nieuwe voorbeeldwoning CSV
                @if($fileType->isBeingProcessed())
                    <span class="glyphicon glyphicon-repeat fast-right-spinner"></span>
                @endif
            </button>

            @if($fileStorage instanceof \App\Models\FileStorage && ! $fileStorage->isBeingProcessed())
                <a href="{{route('cooperation.file-storage.download', ['cooperation' => $cooperation, 'fileStorage' => $fileStorage])}}"
                   class="btn btn-success">
                    Download bestaande voorbeeldwoning CSV ({{$fileStorage->created_at->format('Y-m-d H:i')}})
                </a>
            @endif
        </div>
    </div>
</div>
