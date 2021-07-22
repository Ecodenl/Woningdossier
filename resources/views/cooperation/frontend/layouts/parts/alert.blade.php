<div id="{{$id ?? ''}}" class="w-3/4 p-4 relative bg-white rounded-lg text-sm text-blue border border-solid my-3 {{$class ?? ''}}" role="alert" x-data="{display: true}" x-show="display">
    @if(($dismissible ?? true))
        <div class="absolute right-3 top-3 cursor-pointer" x-on:click="display = false;">
            <i class="icon-md icon-close-circle-light"></i>
        </div>
    @endif

    {{ $slot }}
</div>