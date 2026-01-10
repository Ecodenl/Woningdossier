@extends('cooperation.admin.layouts.app', [
    'panelTitle' => __('cooperation/admin/cooperation/cooperation-admin/scans.index.title')
])

@section('content')
    <form class="flex flex-wrap w-full"
          action="{{route('cooperation.admin.cooperation.cooperation-admin.scans.store')}}"
          method="POST">
        @csrf

        @component('cooperation.frontend.layouts.components.form-group', [
            'withInputSource' => false,
            'label' => __('cooperation/admin/cooperation/cooperation-admin/scans.form.type.label'),
            'id' => 'scans',
            'class' => 'w-full lg:w-1/2 lg:pr-3',
            'inputName' => "scans.type",
        ])
            @component('cooperation.frontend.layouts.components.alpine-select')
                <select class="form-input hidden" name="scans[type]" id="scans">
                    @foreach($mapping as $type => $typeTranslation)
                        <option @if($currentScan === $type) selected @endif value="{{$type}}">{{$typeTranslation}}</option>
                    @endforeach
                </select>
            @endcomponent
        @endcomponent

        {{-- Kleine Maatregelen Instellingen --}}
        <div class="w-full mt-6">
            <h3 class="text-lg font-semibold mb-4">
                @lang('cooperation/admin/cooperation/cooperation-admin/scans.form.small-measures.title')
            </h3>

            <p class="text-sm text-gray-600 mb-4">
                @lang('cooperation/admin/cooperation/cooperation-admin/scans.form.small-measures.description')
            </p>

            @foreach(['quick-scan' => __('cooperation/admin/cooperation/cooperation-admin/scans.form.small-measures.quick-scan'), 'lite-scan' => __('cooperation/admin/cooperation/cooperation-admin/scans.form.small-measures.lite-scan')] as $scanShort => $scanName)
                <div class="flex items-center mb-3">
                    <label class="flex items-center cursor-pointer">
                        <input type="hidden" name="scans[small_measures_enabled][{{ $scanShort }}]" value="0">
                        <input type="checkbox"
                               name="scans[small_measures_enabled][{{ $scanShort }}]"
                               value="1"
                               class="form-checkbox h-5 w-5 text-green-600"
                               @if($smallMeasuresSettings[$scanShort] ?? true) checked @endif>
                        <span class="ml-2">
                            {{ $scanName }}: @lang('cooperation/admin/cooperation/cooperation-admin/scans.form.small-measures.label')
                        </span>
                    </label>
                </div>
            @endforeach
        </div>

        <div class="w-full mt-5">
            <button type="submit" class="btn btn-green">
                @lang('default.buttons.update')
            </button>
        </div>
    </form>
@endsection

