@extends('cooperation.admin.layouts.app', [
    'panelTitle' => __('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.create.title'),
])

@section('content')
    <form class="w-full flex flex-wrap"
          action="{{ route('cooperation.admin.cooperation.cooperation-admin.cooperation-measure-applications.store', compact('type')) }}"
          method="POST">
        @csrf

        @foreach(Hoomdossier::getSupportedLocales() as $locale)
            @component('cooperation.frontend.layouts.components.form-group', [
                'withInputSource' => false,
                'label' => __('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.form.name.label'),
                'class' => 'w-full -mt-5',
                'id' => "name-{$locale}",
                'inputName' => "cooperation_measure_applications.name", //.{$locale},
            ])
                <div class="input-group-prepend">
                    {{ $locale }}
                </div>
                <input id="{{ "name-{$locale}" }}" class="form-input" type="text" name="cooperation_measure_applications[name][{{$locale}}]"
                       placeholder="@lang('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.form.name.placeholder')"
                       value="{{ old("cooperation_measure_applications.name.{$locale}") }}">
            @endcomponent
            @component('cooperation.frontend.layouts.components.form-group', [
                'withInputSource' => false,
                'label' => __('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.form.info.label'),
                'id' => "info-{$locale}",
                'class' => 'w-full',
                'inputName' => "cooperation_measure_applications.info", //.{$locale},
            ])
                <div class="input-group-prepend">
                    {{ $locale }}
                </div>
                <textarea id="{{ "info-{$locale}" }}" class="form-input" type="text" name="cooperation_measure_applications[info][{{$locale}}]"
                          placeholder="@lang('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.form.info.placeholder')"
                >{{ old("cooperation_measure_applications.info.{$locale}") }}</textarea>
            @endcomponent
        @endforeach

        @if($type === \App\Helpers\Models\CooperationMeasureApplicationHelper::SMALL_MEASURE)
            @component('cooperation.frontend.layouts.components.form-group', [
                'withInputSource' => false,
                'label' => __('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.form.measure-category.label'),
                'id' => 'measure-category',
                'class' => 'w-full lg:w-1/2 lg:pr-3',
                'inputName' => "cooperation_measure_applications.measure_category",
            ])
                @component('cooperation.frontend.layouts.components.alpine-select', ['withSearch' => true])
                    <select class="form-input hidden" name="cooperation_measure_applications[measure_category]"
                            id="measure-category">
                        <option value="" disabled selected>
                            @lang('default.form.dropdown.choose')
                        </option>
                        <option value="" class="text-red">
                            @lang('default.form.dropdown.none')
                        </option>
                        @foreach($measures as $measure)
                            <option value="{{ $measure->id }}"
                                    @if(old("cooperation_measure_applications.measure_category") == $measure->id) selected @endif>
                                {{ $measure->name }}
                            </option>
                        @endforeach
                    </select>
                @endcomponent
            @endcomponent

            <div class="w-full"></div>
        @endif

        @component('cooperation.frontend.layouts.components.form-group', [
            'withInputSource' => false,
            'label' => __('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.form.costs-from.label'),
            'id' => "costs-from",
            'class' => 'w-full lg:w-1/2 lg:pr-3',
            'inputName' => "cooperation_measure_applications.costs.from",
        ])
            <div class="input-group-prepend">
                <i class="icon-md icon-moneybag"></i>
            </div>
            <input id="costs-from" class="form-input" type="text" name="cooperation_measure_applications[costs][from]"
                   placeholder="@lang('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.form.costs-from.placeholder')"
                   value="{{ old("cooperation_measure_applications.costs.from") }}">
        @endcomponent
        @component('cooperation.frontend.layouts.components.form-group', [
            'withInputSource' => false,
            'label' => __('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.form.costs-to.label'),
            'id' => "costs-to",
            'class' => 'w-full lg:w-1/2 lg:pl-3',
            'inputName' => "cooperation_measure_applications.costs.to",
        ])
            <div class="input-group-prepend">
                <i class="icon-md icon-moneybag"></i>
            </div>
            <input id="costs-to" class="form-input" type="text" name="cooperation_measure_applications[costs][to]"
                   placeholder="@lang('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.form.costs-to.placeholder')"
                   value="{{ old("cooperation_measure_applications.costs.to") }}">
        @endcomponent

        @component('cooperation.frontend.layouts.components.form-group', [
            'withInputSource' => false,
            'label' => __('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.form.savings.label'),
            'id' => "savings-money",
            'class' => 'w-full lg:w-1/2 lg:pr-3',
            'inputName' => "cooperation_measure_applications.savings_money",
        ])
            <div class="input-group-prepend">
                <i class="icon-md icon-moneybag"></i>
            </div>
            <input id="savings-money" class="form-input" type="text" name="cooperation_measure_applications[savings_money]"
                   placeholder="@lang('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.form.savings.placeholder')"
                   value="{{ old("cooperation_measure_applications.savings_money")}}">
        @endcomponent

        <div class="w-full"></div>

        @php
            $old = old('cooperation_measure_applications.extra.icon', 'icon-account-circle');
        @endphp
        <div class="flex w-full flex-wrap">
            @component('cooperation.frontend.layouts.components.form-group', [
                'withInputSource' => false,
                'label' => __('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.form.icon.label'),
                'id' => 'icon',
                'class' => 'w-full lg:w-1/2 lg:pr-3',
                'inputName' => "cooperation_measure_applications.extra.icon",
            ])
                @component('cooperation.frontend.layouts.components.alpine-select', ['withSearch' => true])
                    <select class="form-input hidden" name="cooperation_measure_applications[extra][icon]"
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
                @lang('default.buttons.store')
                <i class="w-3 h-3 icon-plus-purple ml-1"></i>
            </button>
        </div>
    </form>
@endsection