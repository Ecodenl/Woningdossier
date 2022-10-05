<div>
    <?php
    // We want to unset keys once the input is placed
    $locales = config('hoomdossier.supported_locales');
    // default
    $translationKey = '';
    ?>
    @foreach($locales as $locale)
        <div class="form-group">
            <label for="name-{{ $locale }}">{{ $locale }}:</label>
            <input id="name-{{$locale}}" class="form-control" wire:model="exampleBuildingValues.name.{{$locale}}">
        </div>
        <?php unset($locales[$locale]); ?>

    @endforeach


    <div class="form-group {{ $errors->has('exampleBuildingValues.building_type_id') ? ' has-error' : '' }}">
        <?php
        $selected = old('building_type_id', isset($exampleBuilding) ? $exampleBuilding->building_type_id : null);
        ?>

        <label for="building_type_id">{{\App\Helpers\Translation::translate('building-detail.building-type.what-type.title')}}</label>
        <select id="building_type_id" wire:model="exampleBuildingValues.building_type_id" class="form-control">
            <option value="">-</option>
            @foreach($buildingTypes as $buildingType)
                <option value="{{ $buildingType->id }}">{{ $buildingType->name }}</option>
            @endforeach
        </select>

        @if ($errors->has('exampleBuildingValues.building_type_id'))
            <span class="help-block">
            <strong>{{ $errors->first('exampleBuildingValues.building_type_id') }}</strong>
        </span>
        @endif
    </div>

    <div class="form-group {{ $errors->has('exampleBuildingValues.cooperation_id') ? ' has-error' : '' }}">
        <label for="cooperation">@lang('cooperation/admin/example-buildings.components.cooperation')</label>
        <select id="cooperation" wire:model="exampleBuildingValues.cooperation_id" class="form-control">
            <option value="" selected="selected">-</option>
            @foreach($cooperations as $cooperation)
                <option value="{{ $cooperation->id }}">{{ $cooperation->name }}</option>
            @endforeach
        </select>

        @if ($errors->has('exampleBuildingValues.cooperation_id'))
            <span class="help-block">
            <strong>{{ $errors->first('exampleBuildingValues.cooperation_id') }}</strong>
        </span>
        @endif
    </div>

    <div class="form-group">
        <label for="order">@lang('cooperation/admin/example-buildings.components.order')</label>
        <input type="number" id="order" class="form-control" min="0" name="order"
               wire:model="exampleBuildingValues.order">
    </div>

    <div class="form-group">
        <label for="is_default">@lang('cooperation/admin/example-buildings.components.is-default.label')</label>
        <select wire:model="exampleBuildingValues.is_default" class="form-control">
            @foreach(__('cooperation/admin/example-buildings.components.is-default.options') as $val => $string)
                <option value="{{ $val }}">{{ $string }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group" style="margin-top: 5em;">
        <input type="hidden" name="new" value="0">
        <button wire:click="save" type="submit" class="btn btn-success btn-block">@lang('cooperation/admin/example-buildings.form.update')</button>
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
            @if(isset($exampleBuilding) && $exampleBuilding instanceof \App\Models\ExampleBuilding)
                @foreach($exampleBuilding->contents as $content)
                    <div role="tabpanel" class="tab-pane" id="{{ $content->id }}" wire:ignore.self>
                        @include('cooperation.admin.example-buildings.components.content-table', ['content' => $content])
                    </div>
                @endforeach
            @endif
            <div role="tabpanel" class="tab-pane @if(Route::currentRouteName() === "cooperation.admin.example-buildings.create") active @endif" id="new" wire:ignore.self>
                @include('cooperation.admin.example-buildings.components.content-table', ['content' => null])
            </div>
        </div>
</div>
