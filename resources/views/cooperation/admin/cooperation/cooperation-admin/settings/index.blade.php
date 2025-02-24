@extends('cooperation.admin.layouts.app', [
    'panelTitle' => __('cooperation/admin/cooperation/cooperation-admin/settings.index.title')
])

@section('content')
    @php
        $action = isset($cooperationToManage) && $cooperationToManage instanceof \App\Models\Cooperation
            ? route('cooperation.admin.super-admin.cooperations.cooperation-to-manage.settings.store', compact('cooperation', 'cooperationToManage'))
            : route('cooperation.admin.cooperation.cooperation-admin.settings.store');

        $cooperationToUse = isset($cooperationToManage) && $cooperationToManage instanceof \App\Models\Cooperation
            ? $cooperationToManage : $cooperation;
    @endphp

    <form class="w-full flex flex-wrap"
          action="{{ $action }}"
          enctype="multipart/form-data" method="POST">
        @csrf

        @foreach(MediaHelper::getFillableTagsForClass(\App\Models\Cooperation::class) as $tag)
            @component('cooperation.frontend.layouts.components.form-group', [
                'withInputSource' => false,
                'label' => __("cooperation/admin/cooperation/cooperation-admin/settings.index.{$tag}"),
                'class' => 'w-full -mt-5 lg:w-1/3 lg:pr-6 flex-wrap',
                'id' => "file-{$tag}",
                'inputName' => "medias.{$tag}",
            ])
                <input name="medias[{{ $tag }}]" id="file-{{ $tag }}" type="file" class="form-input"/>

                @if(($image = $cooperationToUse->firstMedia($tag)) instanceof \App\Models\Media)
                    <input type="hidden" name="medias[{{ $tag }}_current]" value="{{ $image->id }}"
                           id="current-{{ $tag }}">
                    <div class="flex w-full items-center">
                        <span class="whitespace-nowrap text-center rounded font-bold bg-green text-white p-1 py-2 text-xs overflow-hidden">
                            @lang('cooperation/admin/cooperation/cooperation-admin/settings.index.current')
                            {{$image->filename}}
                        </span>
                        <span class="text-red cursor-pointer font-bold ml-2"
                              onclick="let currentImage = document.getElementById('current-{{ $tag }}'); currentImage.value = null; currentImage.nextElementSibling.style.display = 'none';">
                            X
                        </span>
                    </div>
                @endif
            @endcomponent
        @endforeach

        @foreach(CooperationSettingHelper::getAvailableSettings() as $short => $type)
            @php
                $kebabShort = Str::kebab(Str::studly($short));
                $setting = $cooperationSettings->where('short', $short)->first();
                $colClass = $type === 'input' ? 'col-md-4' : 'col-xs-12';
            @endphp
            @component('cooperation.frontend.layouts.components.form-group', [
                'withInputSource' => false,
                'label' => __("cooperation/admin/cooperation/cooperation-admin/settings.form.{$kebabShort}.label") . '<small><br>' . __("cooperation/admin/cooperation/cooperation-admin/settings.form.{$kebabShort}.help") . '</small>',
                'class' => 'w-full',
                'inputGroupClass' => $type === 'input' ? 'lg:w-1/2' : '',
                'id' => $kebabShort,
                'inputName' => "cooperation_settings.{$short}",
            ])
                @switch($type)
                    @case('input')
                        <input id="{{$kebabShort}}" type="text"
                               value="{{old("cooperation_settings.{$short}", $setting?->value)}}"
                               class="form-input"
                               placeholder="@lang("cooperation/admin/cooperation/cooperation-admin/settings.form.{$kebabShort}.placeholder")"
                               name="cooperation_settings[{{$short}}]">
                        @break
                    @case('textarea')
                        <textarea id="{{$kebabShort}}" type="text"
                                  class="form-input h-64"
                                  placeholder="@lang("cooperation/admin/cooperation/cooperation-admin/settings.form.{$kebabShort}.placeholder")"
                                  name="cooperation_settings[{{$short}}]"
                        >{{old("cooperation_settings.{$short}", $setting?->value)}}</textarea>
                        @break
                @endswitch
            @endcomponent
        @endforeach

        <div class="w-full mt-5">
            <button type="submit" class="btn btn-green">
                @lang('default.buttons.update')
            </button>
        </div>
    </form>
@endsection

@push('js')
    <script type="module">
        document.addEventListener('DOMContentLoaded', () => {
            let id = '{{ Str::kebab(Str::studly(CooperationSettingHelper::SHORT_VERIFICATION_EMAIL_TEXT)) }}';

            document.getElementById(id).addEventListener('mousedown', function () {
                if (! this.value) {
                    this.value = this.placeholder;
                }
            }, {once: true});
        });
    </script>
@endpush