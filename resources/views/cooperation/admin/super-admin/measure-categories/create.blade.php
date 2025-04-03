@extends('cooperation.admin.layouts.app')

@section('content')
    <form class="w-full flex flex-wrap"
          action="{{route('cooperation.admin.super-admin.measure-categories.store')}}"
          method="POST">
        @csrf

        @foreach(Hoomdossier::getSupportedLocales() as $locale)
            @component('cooperation.frontend.layouts.components.form-group', [
                'withInputSource' => false,
                'label' => __('cooperation/admin/super-admin/measure-categories.form.name.label'),
                'id' => "name-{$locale}",
                'class' => 'w-full -mt-5 lg:w-1/2 lg:pr-3',
                'inputName' => "measure_categories.name", // .{$locale}
            ])
                <div class="input-group-prepend">
                    {{ $locale }}
                </div>
                <input id="{{ "name-{$locale}" }}" name="measure_categories[name][{{$locale}}]"
                       class="form-input"
                       value="{{ old("measure_categories.name.{$locale}") }}"
                       placeholder="@lang('cooperation/admin/super-admin/measure-categories.form.name.placeholder')">
            @endcomponent
        @endforeach
        @component('cooperation.frontend.layouts.components.form-group', [
            'withInputSource' => false,
            //'label' => __('cooperation/admin/super-admin/measure-categories.form.vbjehuis-measure.label'),
            'id' => "vbjehuis-measure",
            'class' => 'w-full -mt-5 lg:w-1/2 lg:pl-3',
            'inputName' => "vbjehuis_measure",
        ])
            @php
                $vbjehuisAvailable = ! empty($measures);
            @endphp
            @slot('label')
                @lang('cooperation/admin/super-admin/measure-categories.form.vbjehuis-measure.label')
                @if(! $vbjehuisAvailable)
                    <small class="text-red">
                        <br> @lang('api.verbeterjehuis.filters.measures.error')
                    </small>
                @endif
            @endslot
            @component('cooperation.frontend.layouts.components.alpine-select', ['withSearch' => true])
                <select id="vbjehuis-measure" name="vbjehuis_measure" class="form-input hidden"
                        @if(! $vbjehuisAvailable) disabled @endif>
                    <option value="" selected disabled>@lang('default.form.dropdown.choose')</option>
                    <option value="" class="text-red">
                        @lang('cooperation/admin/super-admin/measure-categories.form.vbjehuis-measure.option.none')
                    </option>
                    @foreach($measures as $measure)
                        <option value="{{ $measure['Value'] }}"
                                @if(old('vbjehuis_measure') == $measure['Value']) selected @endif
                        >
                            {{ $measure['Label'] }}
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