<div class="flex flex-row flex-wrap absolute inset-0 bg-white items-center justify-center">
    <div class="w-full flex flex-row flex-wrap justify-center items-center">
        <i class="icon-xl {{$icon ?? 'icon-ventilation-fan'}} animate-{{$animation ?? 'spin-slow'}} "></i>
        @if(! empty($label))
            <div class="w-full flex flex-row flex-wrap justify-center items-center mt-5">
                <h3 class="heading-4 text-center">
                    {!! $label !!}
                </h3>
            </div>
        @endif
    </div>
</div>