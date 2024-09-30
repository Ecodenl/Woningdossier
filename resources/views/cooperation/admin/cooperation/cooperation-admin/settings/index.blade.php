@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('cooperation/admin/cooperation/cooperation-admin/settings.index.title')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    @php
                        $action = isset($cooperationToManage) && $cooperationToManage instanceof \App\Models\Cooperation
                            ? route('cooperation.admin.super-admin.cooperations.cooperation-to-manage.settings.store', compact('cooperation', 'cooperationToManage'))
                            : route('cooperation.admin.cooperation.cooperation-admin.settings.store');
                    @endphp

                    <form action="{{ $action }}"
                          enctype="multipart/form-data" method="post">
                        @csrf
                        <div class="row">
                            @foreach(MediaHelper::getFillableTagsForClass(\App\Models\Cooperation::class) as $tag)
                                <div class="col-md-4">
                                    <label for="file-{{ $tag }}">
                                        @lang("cooperation/admin/cooperation/cooperation-admin/settings.index.{$tag}")
                                    </label>
                                    <input name="medias[{{ $tag }}]" id="file-{{ $tag }}" type="file"/>

                                    @if(($image = $cooperation->firstMedia($tag)) instanceof \App\Models\Media)
                                        <input type="hidden" name="medias[{{ $tag }}_current]" value="{{ $image->id }}"
                                               id="current-{{ $tag }}">
                                        <div style="display: flex; align-items: center;">
                                            <span class="label label-primary">
                                                @lang('cooperation/admin/cooperation/cooperation-admin/settings.index.current')
                                                {{$image->filename}}
                                            </span>
                                            <span class="text-danger"
                                                  style="cursor: pointer; font-weight: bold; margin-left: 0.5rem;"
                                                  onclick="let currentImage = document.getElementById('current-{{ $tag }}'); currentImage.value = null; currentImage.nextElementSibling.style.display = 'none';">
                                                X
                                            </span>
                                        </div>
                                    @endif

                                    @if($errors->has("medias.{$tag}"))
                                        {{$errors->first("medias.{$tag}")}}
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <div class="row" style="margin-top: 1rem;">
                            @foreach(CooperationSettingHelper::getAvailableSettings() as $short => $type)
                                @php
                                    $kebabShort = Str::kebab(Str::studly($short));
                                    $setting = $cooperationSettings->where('short', $short)->first();
                                    $colClass = $type === 'input' ? 'col-md-4' : 'col-xs-12';
                                @endphp
                                <div class="{{ $colClass }}">
                                    @component('layouts.parts.components.form-group', [
                                        'input_name' => "cooperation_settings.{$short}",
                                    ])
                                        <label for="{{$kebabShort}}">
                                            @lang("cooperation/admin/cooperation/cooperation-admin/settings.form.{$kebabShort}.label")
                                        </label>
                                        <small>
                                            <br>
                                            @lang("cooperation/admin/cooperation/cooperation-admin/settings.form.{$kebabShort}.help")
                                        </small>
                                        @switch($type)
                                            @case('input')
                                                <input id="{{$kebabShort}}" type="text"
                                                       value="{{old("cooperation_settings.{$short}", $setting?->value)}}"
                                                       class="form-control"
                                                       placeholder="@lang("cooperation/admin/cooperation/cooperation-admin/settings.form.{$kebabShort}.placeholder")"
                                                       name="cooperation_settings[{{$short}}]">
                                                @break
                                            @case('textarea')
                                                <textarea id="{{$kebabShort}}" type="text"
                                                       class="form-control" rows="10"
                                                       placeholder="@lang("cooperation/admin/cooperation/cooperation-admin/settings.form.{$kebabShort}.placeholder")"
                                                       name="cooperation_settings[{{$short}}]"
                                                >{{old("cooperation_settings.{$short}", $setting?->value)}}</textarea>
                                                @break
                                        @endswitch
                                    @endcomponent
                                </div>
                            @endforeach
                        </div>

                        <div class="row mt-20">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-default">
                                    @lang('default.buttons.update')
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
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