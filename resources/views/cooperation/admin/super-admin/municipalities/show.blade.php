@extends('cooperation.admin.layouts.app', [
    'panelTitle' => $municipality->name,
])

@section('content')
    <form class="w-full flex flex-wrap" action="{{ route('cooperation.admin.super-admin.municipalities.couple', compact('municipality')) }}"
          method="POST">
        @csrf
        @method('PUT')

        @component('cooperation.frontend.layouts.components.form-group', [
            'withInputSource' => false,
            'label' => __('cooperation/admin/super-admin/municipalities.form.bag-municipalities.label'),
            'id' => "bag-municipalities",
            'class' => 'w-full -mt-5 lg:w-1/2 lg:pr-3',
            'inputName' => "bag_municipalities",
        ])
            @component('cooperation.frontend.layouts.components.alpine-select', ['withSearch' => true])
                <select id="bag-municipalities" name="bag_municipalities[]"
                       class="form-input hidden" multiple>
                    @foreach($bagMunicipalities as $bagMunicipality)
                        <option value="{{$bagMunicipality->id}}"
                                @if(! empty(old('bag_municipalities')) ? in_array($bagMunicipality->id, old('bag_municipalities')) : $bagMunicipality->target_model_id === $municipality->id) selected @endif
                        >
                            {{ $bagMunicipality->from_value }}
                        </option>
                    @endforeach
                </select>
            @endcomponent
        @endcomponent
        @component('cooperation.frontend.layouts.components.form-group', [
            'withInputSource' => false,
            //'label' => __('cooperation/admin/super-admin/municipalities.form.vbjehuis-municipality.label'),
            'id' => "vbjehuis-municipalities",
            'class' => 'w-full -mt-5 lg:w-1/2 lg:pl-3',
            'inputName' => "vbjehuis_municipality",
        ])
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
                $currentValue = old('vbjehuis_municipality', ! empty($currentMunicipality) ? $currentMunicipality['Id'] . '~' . $currentMunicipality['Name'] : null);
            @endphp
            @slot('label')
                @lang('cooperation/admin/super-admin/municipalities.form.vbjehuis-municipality.label')
                @if(! $vbjehuisAvailable)
                    <small class="text-red">
                        <br> @lang('api.verbeterjehuis.filters.measures.error')
                    </small>
                @endif
            @endslot
            @component('cooperation.frontend.layouts.components.alpine-select', ['withSearch' => true])
                <select name="vbjehuis_municipality" id="vbjehuis-municipality"
                        class="form-input hidden" @if(! $vbjehuisAvailable) disabled @endif>
                    <option value="" disabled selected>
                        @lang('default.form.dropdown.choose')
                    </option>
                    <option value="" class="text-red">
                        @lang('default.form.dropdown.none')
                    </option>
                    @foreach($vbjehuisMunicipalities as $vbjehuisMunicipality)
                        @php $vbjehuisVal = $vbjehuisMunicipality['Id'] . '~' . $vbjehuisMunicipality['Name']; @endphp
                        <option value="{{ $vbjehuisVal }}" @if($vbjehuisVal == $currentValue) selected @endif>
                            {{ $vbjehuisMunicipality['Name'] }}
                        </option>
                    @endforeach
                </select>
            @endcomponent
        @endcomponent

        <div class="w-full mt-5">
            <button class="btn btn-green">
                @lang('default.buttons.save')
            </button>
        </div>
    </form>
@endsection