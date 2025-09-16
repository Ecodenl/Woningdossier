<div>
    @foreach(Hoomdossier::getSupportedLocales() as $locale)
        @component('cooperation.frontend.layouts.components.form-group', [
            'class' => 'w-full -mt-5 sm:w-1/2',
            'label' => __('cooperation/admin/example-buildings.form.name.label'),
            'id' => "name-{$locale}",
            'inputName' => "exampleBuildingValues.name", //.{$locale}
            'withInputSource' => false,
        ])
            <div class="input-group-prepend">
                {{ $locale }}
            </div>
            <input id="{{ "name-{$locale}" }}" class="form-input"
                   wire:model.live.debounce.500ms="{{ "exampleBuildingValues.name.{$locale}" }}">
        @endcomponent
    @endforeach

    @component('cooperation.frontend.layouts.components.form-group', [
        'class' => 'w-full sm:w-1/2',
        'label' => __('building-detail.building-type.what-type.title') . ($isSuperAdmin && isset($exampleBuilding) ? "Huidig: {$exampleBuilding->buildingType->name}" : ''),
        'id' => "building-type-id",
        'inputName' => "exampleBuildingValues.building_type_id",
        'withInputSource' => false,
    ])
        @component('cooperation.frontend.layouts.components.alpine-select')
            <select id="building-type-id" class="form-input hidden"
                    wire:model.live="exampleBuildingValues.building_type_id">
                <option value="">-</option>
                @php
                    $options = $exampleBuilding instanceof \App\Models\ExampleBuilding && $exampleBuilding->isGeneric()
                        ? $genericBuildingTypes : $buildingTypes;
                @endphp
                @foreach($options as $buildingType)
                    <option value="{{ $buildingType->id }}">{{ $buildingType->name }}</option>
                @endforeach
            </select>
        @endcomponent
    @endcomponent

    @if($isSuperAdmin)
        @component('cooperation.frontend.layouts.components.form-group', [
            'class' => 'w-full sm:w-1/2',
            'label' => __('cooperation/admin/example-buildings.components.cooperation'),
            'id' => 'cooperation',
            'inputName' => 'exampleBuildingValues.cooperation_id',
            'withInputSource' => false,
        ])
            @component('cooperation.frontend.layouts.components.alpine-select')
                <select id="cooperation" class="form-input hidden"
                        wire:model.live="exampleBuildingValues.cooperation_id">
                    <option value="">-</option>
                    @foreach($cooperations as $cooperation)
                        <option value="{{ $cooperation->id }}">
                            {{ $cooperation->name }}
                        </option>
                    @endforeach
                </select>
            @endcomponent
        @endcomponent
    @endif

    @component('cooperation.frontend.layouts.components.form-group', [
        'class' => 'w-full sm:w-1/2',
        'label' => __('cooperation/admin/example-buildings.components.order'),
        'id' => 'order',
        'inputName' => 'exampleBuildingValues.order',
        'withInputSource' => false,
    ])
        <input type="number" id="order" class="form-input" min="0"
               wire:model.live.debounce.500ms="exampleBuildingValues.order">
    @endcomponent

    @component('cooperation.frontend.layouts.components.form-group', [
        'class' => 'w-full sm:w-1/2',
        'label' => __('cooperation/admin/example-buildings.components.is-default.label'),
        'id' => 'is-default',
        'inputName' => 'exampleBuildingValues.is_default',
        'withInputSource' => false,
    ])
        @component('cooperation.frontend.layouts.components.alpine-select')
            <select id="is-default" class="form-input hidden" wire:model.live="exampleBuildingValues.is_default">
                @foreach(__('cooperation/admin/example-buildings.components.is-default.options') as $val => $string)
                    <option value="{{ $val }}">{{ $string }}</option>
                @endforeach
            </select>
        @endcomponent
    @endcomponent

    <button wire:click="save" wire:loading.attr="disabled" wire:target="save" type="submit"
            class="btn btn-green w-full my-5">
        @lang('default.buttons.save')
    </button>

    <h4 class="heading-4 mb-2">
        @lang('cooperation/admin/example-buildings.components.contents.title')
    </h4>
    <div x-data="tabs()">
        <nav class="nav-tabs tabs-square" wire:ignore>
            {{-- tabs --}}
            @if($exampleBuilding instanceof \App\Models\ExampleBuilding)
                @foreach($exampleBuilding->contents as $content)
                    <a data-tab="{{ $content->id }}" x-bind="tab"
                       @if(count($errors->get("content.{$content->id}.*")) > 0) style="border: 1px solid #a94442" @endif>
                        {{ $content->build_year }}
                    </a>
                @endforeach
            @endif
            <a x-bind="tab" class="flex items-center" data-tab="new" @if(Route::currentRouteName() === "cooperation.admin.example-buildings.create") x-ref="main-tab" @endif
               @if(count($errors->get("content.new.*")) > 0) style="border: 1px solid #a94442" @endif>
                <i class="icon-sm icon-plus-purple"></i>
            </a>
        </nav>

        <div class="border border-t-0 border-blue/50 rounded-b-lg">
            {{-- tab contents --}}
            @if($exampleBuilding instanceof \App\Models\ExampleBuilding)
                @foreach($exampleBuilding->contents as $content)
                    <div data-tab="{{ $content->id }}" wire:ignore.self x-bind="container">
                        @include('cooperation.admin.example-buildings.components.content-table', ['content' => $content])
                    </div>
                @endforeach
            @endif
            <div x-bind="container" data-tab="new" wire:ignore.self>
                @include('cooperation.admin.example-buildings.components.content-table', ['content' => null])
            </div>
        </div>
    </div>
</div>
