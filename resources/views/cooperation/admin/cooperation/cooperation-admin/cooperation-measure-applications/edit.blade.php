@extends('cooperation.admin.layouts.app')

@section('content')
    <section class="section">
        <div class="container">
            <form action="{{ route('cooperation.admin.cooperation.cooperation-admin.cooperation-measure-applications.update', compact('cooperationMeasureApplication')) }}"
                  method="post">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-sm-6">
                        <a id="leave-creation-tool" href="{{route('cooperation.admin.cooperation.cooperation-admin.cooperation-measure-applications.index')}}" class="btn btn-warning">
                            @lang('woningdossier.cooperation.admin.cooperation.questionnaires.create.leave-creation-tool')
                        </a>
                    </div>
                    <div class="col-sm-6">
                        <button type="submit"  class="btn btn-primary pull-right">
                            @lang('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.edit.title')
                        </button>
                    </div>
                </div>
                <div class="row alert-top-space">
                    <div class="col-md-12">
                        <div class="panel">
                            <div class="panel-body">
                                @foreach(config('hoomdossier.supported_locales') as $locale)
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <div class="form-group {{ $errors->has('cooperation_measure_applications.name.*') ? ' has-error' : '' }}">
                                                <label for="name-{{$locale}}">
                                                    @lang('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.form.name.label')
                                                </label>
                                                <div class="input-group">
                                                    <span class="input-group-addon">{{$locale}}</span>
                                                    <input type="text" class="form-control" id="name-{{$locale}}"
                                                           name="cooperation_measure_applications[name][{{$locale}}]"
                                                           value="{{ old("cooperation_measure_applications.name.{$locale}", $cooperationMeasureApplication->getTranslation('name', $locale))}}"
                                                           placeholder="@lang('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.form.name.placeholder')">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <div class="form-group {{ $errors->has('cooperation_measure_applications.info.*') ? ' has-error' : '' }}">
                                                <label for="info-{{$locale}}">
                                                    @lang('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.form.info.label')
                                                </label>
                                                <div class="input-group">
                                                    <span class="input-group-addon">{{$locale}}</span>
                                                    <textarea class="form-control" id="info-{{$locale}}"
                                                           name="cooperation_measure_applications[info][{{$locale}}]"
                                                           placeholder="@lang('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.form.info.placeholder')"
                                                    >{{ old("cooperation_measure_applications.info.{$locale}", $cooperationMeasureApplication->getTranslation('info', $locale))}}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group {{ $errors->has('cooperation_measure_applications.costs.from') ? ' has-error' : '' }}">
                                            <label for="costs-from">
                                                @lang('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.form.costs-from.label')
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="icon-sm icon-moneybag"></i></span>
                                                <input type="text" class="form-control" id="costs-from"
                                                       name="cooperation_measure_applications[costs][from]"
                                                       value="{{ old("cooperation_measure_applications.costs.from", $cooperationMeasureApplication->costs['from'] ?? '')}}"
                                                       placeholder="@lang('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.form.costs-from.placeholder')">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group {{ $errors->has('cooperation_measure_applications.costs.to') ? ' has-error' : '' }}">
                                            <label for="costs-to">
                                                @lang('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.form.costs-to.label')
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="icon-sm icon-moneybag"></i></span>
                                                <input type="text" class="form-control" id="costs-to"
                                                       name="cooperation_measure_applications[costs][to]"
                                                       value="{{ old("cooperation_measure_applications.costs.to", $cooperationMeasureApplication->costs['to'] ?? '')}}"
                                                       placeholder="@lang('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.form.costs-to.placeholder')">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group {{ $errors->has('cooperation_measure_applications.savings_money') ? ' has-error' : '' }}">
                                            <label for="savings-money">
                                                @lang('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.form.savings.label')
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="icon-sm icon-moneybag"></i></span>
                                                <input type="text" class="form-control" id="savings-money"
                                                       name="cooperation_measure_applications[savings_money]"
                                                       value="{{ old("cooperation_measure_applications.savings_money", $cooperationMeasureApplication->savings_money)}}"
                                                       placeholder="@lang('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.form.savings.placeholder')">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group {{ $errors->has('cooperation_measure_applications.extra.icon') ? ' has-error' : '' }}">
                                            <label for="icon">
                                                @lang('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.form.icon.label')
                                            </label>
                                            <select class="form-control" id="icon"
                                                    name="cooperation_measure_applications[extra][icon]">
                                                @foreach(File::allFiles(public_path('icons')) as $file)
                                                    @php
                                                        $iconName = "icon-" . str_replace(".{$file->getExtension()}", '', $file->getBasename());
                                                    @endphp
                                                    <option @if(old('cooperation_measure_applications.extra.icon', $cooperationMeasureApplication->extra['icon'] ?? '') === $iconName) selected @endif>
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
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
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