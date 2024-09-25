@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('cooperation/admin/cooperation/cooperation-admin/settings.index.title')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <form action="{{route('cooperation.admin.cooperation.cooperation-admin.settings.store')}}"
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
                            @foreach(CooperationSettingHelper::getAvailableSettings() as $short)
                                @php 
                                    $kebabShort = Str::kebab(Str::studly($short));
                                    $setting = $cooperationSettings->where('short', $short)->first();
                                @endphp
                                <div class="col-md-4">
                                    @component('layouts.parts.components.form-group', [
                                        'input_name' => "cooperation_settings.{$short}",
                                    ])
                                        <label for="{{$kebabShort}}">
                                            @lang("cooperation/admin/cooperation/cooperation-admin/settings.form.{$kebabShort}.label")
                                        </label>
                                        <input id="{{$kebabShort}}" type="text"
                                               value="{{old("cooperation_settings.{$short}", $setting?->value)}}"
                                               class="form-control"
                                               placeholder="@lang("cooperation/admin/cooperation/cooperation-admin/settings.form.{$kebabShort}.placeholder")"
                                               name="cooperation_settings[{{$short}}]">
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

