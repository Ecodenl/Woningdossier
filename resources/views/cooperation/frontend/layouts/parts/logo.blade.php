<?php
    $logo = $cooperation->firstMedia(MediaConstants::LOGO);
?>
<div class="flex flex-wrap w-full justify-center items-center">
    <div class="w-36 h-36 flex flex-wrap justify-center items-center">
        @if($logo instanceof \App\Models\Media)
            <img src="{{ $logo->getUrl() }}" alt="{{ $cooperation->name }}">
        @else
            <h4 class="heading-4">
                {{ $cooperation->name }}
            </h4>
        @endif
    </div>
</div>
