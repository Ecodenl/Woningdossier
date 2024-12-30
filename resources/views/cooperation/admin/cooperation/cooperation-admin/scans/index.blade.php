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

        <div class="w-full mt-5">
            <button type="submit" class="btn btn-green">
                @lang('default.buttons.update')
            </button>
        </div>
    </form>
@endsection

