@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('cooperation/admin/super-admin/measure-applications.edit.title')
        </div>

        <div class="panel-body">
            <form action="{{route('cooperation.admin.super-admin.measure-applications.update', compact('measureApplication'))}}"
                  method="post">
                @csrf
                @method('PUT')

                @foreach(config('hoomdossier.supported_locales') as $locale)
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="form-group {{ $errors->has('measure_applications.measure_name.*') ? ' has-error' : '' }}">
                                <label for="name-{{$locale}}">
                                    @lang('cooperation/admin/super-admin/measure-applications.form.measure-name.label')
                                </label>
                                <div class="input-group">
                                    <span class="input-group-addon">{{$locale}}</span>
                                    <input type="text" class="form-control" id="name-{{$locale}}"
                                           name="measure_applications[measure_name][{{$locale}}]"
                                           value="{{ old("measure_applications.measure_name.{$locale}", $measureApplication->getTranslation('measure_name', $locale))}}"
                                           placeholder="@lang('cooperation/admin/super-admin/measure-applications.form.measure-name.placeholder')">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="form-group {{ $errors->has('measure_applications.measure_info.*') ? ' has-error' : '' }}">
                                <label for="info-{{$locale}}">
                                    @lang('cooperation/admin/super-admin/measure-applications.form.measure-info.label')
                                </label>
                                <div class="input-group">
                                    <span class="input-group-addon">{{$locale}}</span>
                                    <textarea class="form-control" id="info-{{$locale}}"
                                              name="measure_applications[measure_info][{{$locale}}]"
                                              placeholder="@lang('cooperation/admin/super-admin/measure-applications.form.measure-info.placeholder')"
                                    >{{ old("measure_applications.measure_info.{$locale}", $measureApplication->getTranslation('measure_info', $locale))}}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group {{ $errors->has('measure_applications.configurations.icon') ? ' has-error' : '' }}">
                            <label for="icon">
                                @lang('cooperation/admin/super-admin/measure-applications.form.icon.label')
                            </label>
                            <select class="form-control" id="icon"
                                    name="measure_applications[configurations][icon]">
                                @foreach(\Illuminate\Support\Facades\File::allFiles(public_path('icons')) as $file)
                                    @php
                                        $iconName = "icon-" . str_replace(".{$file->getExtension()}", '', $file->getBasename());
                                    @endphp
                                    <option @if(old('measure_applications.configurations.icon', $measureApplication->configurations['icon'] ?? '') === $iconName) selected @endif>
                                        {{ $iconName }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <i id="icon-preview" style="margin-top: 2.6rem;"></i>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <button class="btn btn-primary">
                            @lang('cooperation/admin/super-admin/measure-applications.edit.title')
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(() => {
            var $icon = $('#icon');
            $icon.select2();
            $icon.change(function () {
                var $iconPreview = $('#icon-preview');
                $iconPreview.removeClass();
                var icon = $(this).val();
                $iconPreview.addClass(`icon-lg ${icon}`);
            });
            $icon.trigger('change');
        });
    </script>
@endpush