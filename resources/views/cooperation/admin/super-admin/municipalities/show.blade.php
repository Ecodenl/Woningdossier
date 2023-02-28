@extends('cooperation.admin.layouts.app')

@section('content')
    <section class="section">
        <div class="container">
            <form action="{{ route('cooperation.admin.super-admin.municipalities.couple', compact('municipality')) }}"
                  method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-sm-6">
                        <a id="leave-creation-tool" class="btn btn-warning"
                           href="{{route('cooperation.admin.super-admin.municipalities.index')}}">
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
                            <div class="panel-heading">
                                {{ $municipality->name }}
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-xs-6">
                                        @component('layouts.parts.components.form-group', [
                                            'input_name' => 'bag_municipalities'
                                        ])
                                            <label for="bag-municipalities">
                                                @lang('cooperation/admin/super-admin/municipalities.form.bag-municipalities.label')
                                            </label>
                                            <select name="bag_municipalities[]" id="bag-municipalities"
                                                    class="form-control" multiple="multiple">
                                                @foreach($bagMunicipalities as $bagMunicipality)
                                                    <option value="{{$bagMunicipality->id}}"
                                                            @if(! empty(old('bag_municipalities')) ? in_array($bagMunicipality->id, old('bag_municipalities')) : $bagMunicipality->target_model_id === $municipality->id) selected="selected" @endif
                                                    >
                                                        {{ $bagMunicipality->from_value }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if($errors->has('bag_municipalities.*'))
                                                <div class="has-error">
                                                    <span class="help-block">
                                                        <strong>
                                                            {{ $errors->first('bag_municipalities.*') }}
                                                        </strong>
                                                    </span>
                                                </div>
                                            @endif
                                        @endcomponent
                                    </div>
                                    <div class="col-xs-6">
                                        @php
                                            $vbjehuisAvailable = ! empty($vbjehuisMunicipalities);
                                            if (! $vbjehuisAvailable && $mappedVbjehuisMunicipality instanceof \App\Models\Mapping) {
                                                $vbjehuisMunicipalities = [
                                                    $mappedVbjehuisMunicipality->target_data,
                                                ];
                                            }
                                            // Multiple municipalities can have the same ID. We check the name to show
                                            // the "correct" value.
                                            $currentMunicipality = $mappedVbjehuisMunicipality->target_data ?? [];
                                            $currentValue = old('vbjehuis_municipality', ! empty($currentMunicipality) ? $currentMunicipality['Id'] . '-' . $currentMunicipality['Name'] : null);
                                        @endphp
                                        @component('layouts.parts.components.form-group', [
                                            'input_name' => 'vbjehuis_municipality'
                                        ])
                                            <label for="vbjehuis-municipality">
                                                @lang('cooperation/admin/super-admin/municipalities.form.vbjehuis-municipality.label')
                                            </label>
                                            @if(! $vbjehuisAvailable)
                                                <small class="text-danger">
                                                    <br> @lang('api.verbeterjehuis.filters.cities.error')
                                                </small>
                                            @endif
                                            <select name="vbjehuis_municipality" id="vbjehuis-municipality"
                                                    class="form-control" @if(! $vbjehuisAvailable) disabled @endif>
                                                <option></option>
                                                @foreach($vbjehuisMunicipalities as $vbjehuisMunicipality)
                                                    @php $vbjehuisVal = $vbjehuisMunicipality['Id'] . '-' . $vbjehuisMunicipality['Name']; @endphp
                                                    <option value="{{ $vbjehuisVal }}"
                                                            @if($vbjehuisVal == $currentValue) selected="selected" @endif
                                                    >
                                                        {{ $vbjehuisMunicipality['Name'] }}
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
        $(document).ready(() => {
            $('#bag-municipalities').select2({});
            $('#vbjehuis-municipality').select2({
                allowClear: true,
                placeholder: '@lang('default.form.dropdown.choose')'
            });
        });
    </script>
@endpush