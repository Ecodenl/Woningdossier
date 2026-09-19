{{-- Logo bar: Hoomdossier on the left, the cooperation on the right. Navigation lives one row down. --}}
<div class="flex flex-row flex-wrap justify-between items-center w-full bg-white px-5 xl:px-20 py-4 relative z-150">
    <a href="{{ route('cooperation.welcome') }}" class="flex items-center">
        <i class="icon-hoomdossier"></i>
    </a>

    @php
        /** @var \App\Models\Cooperation $cooperation */
        $cooperationLogo = $cooperation->firstMedia(MediaHelper::LOGO);
    @endphp
    <div class="flex items-center">
        @if($cooperationLogo instanceof \App\Models\Media)
            <img src="{{ route('cooperation.media.serve', ['cooperation' => $cooperation, 'media' => $cooperationLogo]) }}"
                 alt="{{ $cooperation->name }}" class="max-h-16 w-auto">
        @else
            <h4 class="heading-4 mb-0">
                {{ $cooperation->name }}
            </h4>
        @endif
    </div>
</div>
