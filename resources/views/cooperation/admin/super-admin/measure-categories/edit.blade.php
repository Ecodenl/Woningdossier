@extends('cooperation.admin.layouts.app')

@section('content')
    <section class="section">
        <div class="container">
            <form action="{{ route('cooperation.admin.super-admin.measure-categories.update', compact('measureCategory')) }}"
                  method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-sm-6">
                        <a id="leave-creation-tool" class="btn btn-warning"
                           href="{{route('cooperation.admin.super-admin.measure-categories.index')}}">
                            @lang('woningdossier.cooperation.admin.cooperation.questionnaires.create.leave-creation-tool')
                        </a>
                    </div>
                    <div class="col-sm-6">
                        <button type="submit" class="btn btn-primary pull-right">
                            @lang('default.buttons.save')
                        </button>
                    </div>
                </div>
                <div class="row alert-top-space">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-xs-6">
                                        @foreach(config('hoomdossier.supported_locales') as $locale)
                                            @component('layouts.parts.components.form-group', [
                                                'input_name' => "measure_categories.name.{$locale}"
                                            ])
                                                <label for="name-{{$locale}}">
                                                    @lang('cooperation/admin/super-admin/measure-categories.form.name.label')
                                                </label>
                                                <input type="text" class="form-control" id="name-{{$locale}}"
                                                       name="measure_categories[name][{{$locale}}]"
                                                       value="{{ old("measure_categories.name.{$locale}", $measureCategory->getTranslation('name', $locale)) }}"
                                                       placeholder="@lang('cooperation/admin/super-admin/measure-categories.form.name.placeholder')">
                                            @endcomponent
                                        @endforeach
                                    </div>
                                    <div class="col-xs-6">
                                        @php
                                            $vbjehuisAvailable = ! empty($measures);
                                            if (! $vbjehuisAvailable && $currentMapping instanceof \App\Models\Mapping) {
                                                $measures = [
                                                    $currentMapping->target_data,
                                                ];
                                            }
                                        @endphp
                                        @component('layouts.parts.components.form-group', [
                                            'input_name' => 'vbjehuis_measure'
                                        ])
                                            <label for="vbjehuis-measure">
                                                @lang('cooperation/admin/super-admin/measure-categories.form.vbjehuis-measure.label')
                                            </label>
                                            @if(! $vbjehuisAvailable)
                                                <small class="text-danger">
                                                    <br> @lang('api.verbeterjehuis.filters.measures.error')
                                                </small>
                                            @endif
                                            <select name="vbjehuis_measure" id="vbjehuis-measure"
                                                    class="form-control" @if(! $vbjehuisAvailable) disabled @endif>
                                                <option></option>
                                                @foreach($measures as $measure)
                                                    <option value="{{ $measure['Value'] }}"
                                                            @if(old('vbjehuis_measure', $currentMapping->target_data['Value'] ?? null) == $measure['Value']) selected="selected" @endif
                                                    >
                                                        {{ $measure['Label'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @endcomponent
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
        $(document).ready(function () {
            $('#vbjehuis-measure').select2({
                allowClear: true,
                placeholder: '@lang('default.form.dropdown.choose')'
            });
        });
    </script>
@endpush