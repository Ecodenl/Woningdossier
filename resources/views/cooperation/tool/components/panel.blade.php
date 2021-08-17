<div class="flex flex-row flex-wrap w-full border border-solid border-green rounded-lg">
    <div class="flex flex-row flex-wrap w-full items-center bg-green text-white h-11 px-5 rounded-t-lg">
        {!! $label !!}
    </div>
    <div class="flex flex-row flex-wrap w-full items-center bg-white px-5 py-8 rounded-b-lg">
        {{ $slot }}
    </div>
</div>