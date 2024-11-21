<div>
    @foreach(config('hoomdossier.supported_locales') as $locale)
        @component('layouts.parts.components.form-group', [
            'input_name' => "exampleBuildingValues.name.{$locale}"
        ])
            <label for="name-{{ $locale }}">{{ $locale }}:</label>
            <input id="name-{{ $locale }}" class="form-control" wire:model.live="exampleBuildingValues.name.{{$locale}}">
        @endcomponent
    @endforeach

    <div class="form-group {{ $errors->has('exampleBuildingValues.building_type_id') ? 'has-error' : '' }}">
        @php
            $selected = old('building_type_id', isset($exampleBuilding) ? $exampleBuilding->building_type_id : null);
        @endphp

        <label for="building_type_id">
            @lang('building-detail.building-type.what-type.title')
            @if($isSuperAdmin && isset($exampleBuilding))
                Huidig: {{$exampleBuilding->buildingType->name}}
            @endif
        </label>
        <select id="building_type_id" wire:model.live="exampleBuildingValues.building_type_id" class="form-control">
            <option value="">-</option>
            @if($exampleBuilding instanceof \App\Models\ExampleBuilding && $exampleBuilding->isGeneric())
                @foreach($genericBuildingTypes as $buildingType)
                    <option value="{{ $buildingType->id }}">{{ $buildingType->name }}</option>
                @endforeach
            @else
                @foreach($buildingTypes as $buildingType)
                    <option value="{{ $buildingType->id }}">{{ $buildingType->name }}</option>
                @endforeach
            @endif
        </select>

        @if($errors->has('exampleBuildingValues.building_type_id'))
            <span class="help-block">
                <strong>{{ $errors->first('exampleBuildingValues.building_type_id') }}</strong>
            </span>
        @endif
    </div>

    @if($isSuperAdmin)
        @component('layouts.parts.components.form-group', [
            'input_name' => 'exampleBuildingValues.cooperation_id'
        ])
            <label for="cooperation">@lang('cooperation/admin/example-buildings.components.cooperation')</label>
            <select id="cooperation" wire:model.live="exampleBuildingValues.cooperation_id" class="form-control">
                @if($isSuperAdmin)
                    <option value="" selected="selected">-</option>
                @endif
                @foreach($cooperations as $cooperation)
                    <option value="{{ $cooperation->id }}">
                        {{ $cooperation->name }}
                    </option>
                @endforeach
            </select>
        @endcomponent
    @endif

    @component('layouts.parts.components.form-group', [
        'input_name' => 'exampleBuildingValues.order'
    ])
        <label for="order">@lang('cooperation/admin/example-buildings.components.order')</label>
        <input type="number" id="order" class="form-control" min="0" wire:model.live="exampleBuildingValues.order">
    @endcomponent

    @component('layouts.parts.components.form-group', [
        'input_name' => 'exampleBuildingValues.is_default'
    ])
        <label for="is_default">@lang('cooperation/admin/example-buildings.components.is-default.label')</label>
        <select wire:model.live="exampleBuildingValues.is_default" class="form-control">
            @foreach(__('cooperation/admin/example-buildings.components.is-default.options') as $val => $string)
                <option value="{{ $val }}">{{ $string }}</option>
            @endforeach
        </select>
    @endcomponent

    <div class="form-group" style="margin-top: 5em;">
        <input type="hidden" name="new" value="0">
        <button wire:click="save" wire:loading.attr="disabled" wire:target="save" type="submit"
                class="btn btn-success btn-block">
            @lang('cooperation/admin/example-buildings.form.update')
        </button>
    </div>

    <h4>@lang('cooperation/admin/example-buildings.components.contents.title')</h4>
    <ul class="nav nav-tabs" role="tablist" wire:ignore>
        {{-- tabs --}}
        @if($exampleBuilding instanceof \App\Models\ExampleBuilding)
            @foreach($exampleBuilding->contents as $content)
                <li role="presentation">
                    <a href="#{{ $content->id }}" aria-controls="{{ $content->id }}" role="tab" data-toggle="tab"
                       @if(count($errors->get("content.{$content->id}.*")) > 0) style="border: 1px solid #a94442" @endif>
                        {{ $content->build_year }}
                    </a>
                </li>
            @endforeach
        @endif
        <li class="@if(Route::currentRouteName() === "cooperation.admin.example-buildings.create") active @endif">
            <a href="#new" aria-controls="new" role="tab" data-toggle="tab"
               @if(count($errors->get("content.new.*")) > 0) style="border: 1px solid #a94442" @endif>
                <i class="glyphicon glyphicon-plus"></i>
            </a>
        </li>
    </ul>
    <div class="tab-content">
        {{-- tab contents --}}
        @if($exampleBuilding instanceof \App\Models\ExampleBuilding)
            @foreach($exampleBuilding->contents as $content)
                <div role="tabpanel" class="tab-pane" id="{{ $content->id }}" wire:ignore.self>
                    @include('cooperation.admin.example-buildings.components.content-table', ['content' => $content])
                </div>
            @endforeach
        @endif
        <div role="tabpanel"
             class="tab-pane @if(Route::currentRouteName() === "cooperation.admin.example-buildings.create") active @endif"
             id="new" wire:ignore.self>
            @include('cooperation.admin.example-buildings.components.content-table', ['content' => null])
        </div>
    </div>
</div>
