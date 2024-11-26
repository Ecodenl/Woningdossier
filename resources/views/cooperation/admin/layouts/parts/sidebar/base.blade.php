<div id="sidebar" class="w-2/12">
    <div class="w-full border border-solid border-blue-500 border-opacity-50 rounded-lg p-2" x-data="{open: true}">
        {{-- TODO: HasBreadrumbs > slot --}}

{{--        @if(isset($breadcrumbs))--}}
{{--            <div class="col-md-12">--}}
{{--                <ol class="breadcrumb">--}}
{{--                    @foreach($breadcrumbs as $breadcrumb)--}}
{{--                        <li {{Route::currentRouteName() == $breadcrumb['route'] ? 'class="active"' : ''}}>--}}
{{--                            @if(Route::currentRouteName() == $breadcrumb['route'])--}}
{{--                                <a href="{{$breadcrumb['url']}}">{{$breadcrumb['name']}}</a>--}}
{{--                            @else--}}
{{--                                {{$breadcrumb['name']}}--}}
{{--                            @endif--}}

{{--                        </li>--}}
{{--                    @endforeach--}}
{{--                </ol>--}}
{{--            </div>--}}
{{--        @endif--}}

        <div class="flex items-center cursor-pointer" x-on:click="open = !open">
            <h3 class="heading-5 inline-block mr-2 select-none">
                {{ $sidebarTitle }}
            </h3>
            <i x-show="open == false" class="icon-sm icon-arrow-down cursor-pointer select-none" x-on:click="toggle()"></i>
            <i x-cloak x-show="open == true" class="icon-sm icon-arrow-up cursor-pointer select-none" x-on:click="toggle()"></i>
        </div>

        <ul x-show="open">
            {{ $slot }}
        </ul>
    </div>
</div>