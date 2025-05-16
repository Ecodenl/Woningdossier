@extends('cooperation.admin.layouts.app', [
    'panelTitle' => __('cooperation/admin/super-admin/measure-applications.edit.title')
])

@section('content')
    <form class="w-full flex flex-wrap"
          action="{{route('cooperation.admin.super-admin.measure-applications.update', compact('measureApplication'))}}"
          method="POST">
        @csrf
        @method('PUT')

        @foreach(Hoomdossier::getSupportedLocales() as $locale)
            @component('cooperation.frontend.layouts.components.form-group', [
                'withInputSource' => false,
                'label' => __('cooperation/admin/super-admin/measure-applications.form.measure-name.label'),
                'class' => 'w-full -mt-5',
                'id' => "name-{$locale}",
                'inputName' => "measure_applications.measure_name.name", //.{$locale},
            ])
                <div class="input-group-prepend">
                    {{ $locale }}
                </div>
                <input id="{{ "name-{$locale}" }}" class="form-input" type="text" name="measure_applications[measure_name][{{$locale}}]"
                       value="{{ old("measure_applications.measure_name.{$locale}", $measureApplication->getTranslation('measure_name', $locale))}}"
                       placeholder="@lang('cooperation/admin/super-admin/measure-applications.form.measure-name.placeholder')">
            @endcomponent
            @component('cooperation.frontend.layouts.components.form-group', [
                'withInputSource' => false,
                'label' => __('cooperation/admin/super-admin/measure-applications.form.measure-info.label'),
                'id' => "info-{$locale}",
                'class' => 'w-full',
                'inputName' => "measure_applications.measure_info", //.{$locale},
            ])
                <div class="input-group-prepend">
                    {{ $locale }}
                </div>
                <textarea id="{{ "info-{$locale}" }}" class="form-input" type="text" name="measure_applications[measure_info][{{$locale}}]"
                          placeholder="@lang('cooperation/admin/super-admin/measure-applications.form.measure-info.placeholder')"
                >{{ old("measure_applications.measure_info.{$locale}", $measureApplication->getTranslation('measure_info', $locale))}}</textarea>
            @endcomponent
        @endforeach

        @if(! $measureApplication->has_calculations)
            @component('cooperation.frontend.layouts.components.form-group', [
                'withInputSource' => false,
                'label' => __('cooperation/admin/super-admin/measure-applications.form.costs-from.label'),
                'id' => 'costs-from',
                'class' => 'w-full lg:w-1/2 lg:pr-3',
                'inputName' => "measure_applications.cost_range.from",
            ])
                <div class="input-group-prepend">
                    <i class="icon-md icon-moneybag"></i>
                </div>
                <input id="costs-from" type="text" class="form-input"
                       name="measure_applications[cost_range][from]"
                       value="{{ old("measure_applications.cost_range.from", $measureApplication->cost_range['from'] ?? '')}}"
                       placeholder="@lang('cooperation/admin/super-admin/measure-applications.form.costs-from.placeholder')">
            @endcomponent
            @component('cooperation.frontend.layouts.components.form-group', [
                'withInputSource' => false,
                'label' => __('cooperation/admin/super-admin/measure-applications.form.costs-to.label'),
                'id' => 'costs-to',
                'class' => 'w-full lg:w-1/2 lg:pl-3',
                'inputName' => "measure_applications.cost_range.to",
            ])
                <div class="input-group-prepend">
                    <i class="icon-md icon-moneybag"></i>
                </div>
                <input id="costs-to" type="text" class="form-input"
                       name="measure_applications[cost_range][to]"
                       value="{{ old("measure_applications.cost_range.to", $measureApplication->cost_range['to'] ?? '')}}"
                       placeholder="@lang('cooperation/admin/super-admin/measure-applications.form.costs-to.placeholder')">
            @endcomponent

            @component('cooperation.frontend.layouts.components.form-group', [
                'withInputSource' => false,
                'label' => __('cooperation/admin/super-admin/measure-applications.form.savings.label'),
                'id' => "savings-money",
                'class' => 'w-full lg:w-1/2 lg:pr-3',
                'inputName' => "measure_applications.savings_money",
            ])
                <div class="input-group-prepend">
                    <i class="icon-md icon-moneybag"></i>
                </div>
                <input id="savings-money" class="form-input" type="text" name="measure_applications[savings_money]"
                       placeholder="@lang('cooperation/admin/super-admin/measure-applications.form.savings.placeholder')"
                       value="{{ old("measure_applications.savings_money", $measureApplication->savings_money)}}">
            @endcomponent

            <div class="w-full"></div>
        @endif

        @php
            $old = old('measure_applications.configurations.icon', $measureApplication->configurations['icon'] ?? 'icon-account-circle');
        @endphp
        <div class="flex w-full flex-wrap">
            @component('cooperation.frontend.layouts.components.form-group', [
                'withInputSource' => false,
                'label' => __('cooperation/admin/super-admin/measure-applications.form.icon.label'),
                'id' => 'icon',
                'class' => 'w-full lg:w-1/2 lg:pr-3',
                'inputName' => "measure_applications.configurations.icon",
            ])
                @component('cooperation.frontend.layouts.components.alpine-select', ['withSearch' => true])
                    <select class="form-input hidden" name="measure_applications[configurations][icon]"
                            id="icon">
                        @foreach(File::allFiles(public_path('icons')) as $file)
                            @php
                                $iconName = "icon-" . str_replace(".{$file->getExtension()}", '', $file->getBasename());
                            @endphp
                            <option value="{{ $iconName }}" @if($old === $iconName) selected @endif>
                                {{ $iconName }}
                            </option>
                        @endforeach
                    </select>
                @endcomponent
            @endcomponent
        </div>

        <div class="w-full mt-5">
            <button class="btn btn-green flex justify-center items-center" type="submit">
                @lang('cooperation/admin/super-admin/measure-applications.edit.title')
            </button>
        </div>
    </form>
@endsection