@extends('cooperation.admin.layouts.app')

@section('content')
    <form class="w-full flex flex-wrap"
          action="{{ route('cooperation.admin.super-admin.municipalities.store') }}"
          method="POST">
        @csrf

        @component('cooperation.frontend.layouts.components.form-group', [
            'withInputSource' => false,
            'label' => __('cooperation/admin/super-admin/municipalities.form.name.label'),
            'id' => "name",
            'class' => 'w-full -mt-5 lg:w-1/2 lg:pr-3',
            'inputName' => "municipalities.name",
        ])
            <input id="name" name="municipalities[name]"
                   class="form-input"
                   value="{{ old("municipalities.name") }}"
                   placeholder="@lang('cooperation/admin/super-admin/municipalities.form.name.placeholder')">
        @endcomponent

        <div class="w-full mt-5">
            <button class="btn btn-green">
                @lang('default.buttons.save')
            </button>
        </div>
    </form>
@endsection