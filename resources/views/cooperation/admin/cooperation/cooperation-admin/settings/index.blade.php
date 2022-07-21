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
                            <div class="col-md-4">
                                <label for="file-logo">
                                    @lang('cooperation/admin/cooperation/cooperation-admin/settings.index.logo')
                                </label>
                                <input name="medias[logo]" id="file-logo" type="file"/>

                                <span class="label label-primary">
                                    @lang('cooperation/admin/cooperation/cooperation-admin/settings.index.current')
                                    {{optional($cooperation->firstMedia(MediaHelper::LOGO))->filename}}
                                </span>

                                @if($errors->has('medias.logo'))
                                    {{$errors->first('medias.logo')}}
                                @endif
                            </div>
                            <div class="col-md-4">
                                <label for="file-background">
                                    @lang('cooperation/admin/cooperation/cooperation-admin/settings.index.background')
                                </label>
                                <input name="medias[background]" id="file-background" type="file"/>
                                <span class="label label-primary">
                                   @lang('cooperation/admin/cooperation/cooperation-admin/settings.index.current')
                                    {{optional($cooperation->firstMedia(MediaHelper::BACKGROUND))->filename}}
                                </span>
                                @if($errors->has('medias.background'))
                                    {{$errors->first('medias.background')}}
                                @endif
                            </div>
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
                                               value="{{old("cooperation_settings.{$short}", optional($setting)->value)}}"
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

