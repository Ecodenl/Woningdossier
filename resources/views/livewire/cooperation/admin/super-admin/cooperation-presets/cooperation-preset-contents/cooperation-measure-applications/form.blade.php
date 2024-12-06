@push('css')
    {{-- Styling to ensure the hidden select2 element doesn't inherit an incorrect width --}}
    <style>
        span.select2-container {
            width: 100% !important;
        }
    </style>
@endpush
<div>
    <form wire:submit="save()" autocomplete="off">
        <div class="row">
            <div class="col-sm-6">
                <a id="leave-creation-tool" class="btn btn-warning"
                   href="{{route('cooperation.admin.super-admin.cooperation-presets.show', compact('cooperation', 'cooperationPreset'))}}">
                    @lang('woningdossier.cooperation.admin.cooperation.questionnaires.create.leave-creation-tool')
                </a>
            </div>
            <div class="col-sm-6">
                <button type="submit" class="btn btn-primary pull-right">
                    @lang('default.buttons.save')
                </button>
            </div>
        </div>
        <div class="row alert-top-space">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        @if($cooperationPresetContent->exists)
                            @lang('cooperation/admin/super-admin/cooperation-preset-contents.edit.title')
                        @else
                            @lang('cooperation/admin/super-admin/cooperation-preset-contents.create.title')
                        @endif
                    </div>
                    <div class="panel-body">
                        @foreach(config('hoomdossier.supported_locales') as $locale)
                            <div class="row">
                                <div class="col-xs-12">
                                    @component('layouts.parts.components.form-group', ['input_name' => "content.name.{$locale}"])
                                        <label for="name-{{$locale}}">
                                            @lang('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.form.name.label')
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-addon">{{$locale}}</span>
                                            <input type="text" class="form-control" id="name-{{$locale}}"
                                                   wire:model.live="content.name.{{$locale}}"
                                                   placeholder="@lang('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.form.name.placeholder')">
                                        </div>
                                    @endcomponent
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    @component('layouts.parts.components.form-group', ['input_name' => "content.info.{$locale}"])
                                        <label for="info-{{$locale}}">
                                            @lang('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.form.info.label')
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-addon">{{$locale}}</span>
                                            <textarea class="form-control" id="info-{{$locale}}"
                                                      wire:model.live="content.info.{{$locale}}"
                                                      placeholder="@lang('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.form.info.placeholder')"
                                            ></textarea>
                                        </div>
                                    @endcomponent
                                </div>
                            </div>
                        @endforeach
                        <div class="row" @if($content['is_extensive_measure']) style="display: none;" @endif>
                            <div class="col-sm-6">
                                @component('layouts.parts.components.form-group', ['input_name' => 'content.relations.mapping.measure_category'])
                                    <label for="measure-category">
                                        @lang('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.form.measure-category.label')
                                    </label>
                                    <div wire:ignore>
                                        {{-- Wire:ignore here so the select2 doesn't die, but still allows error messages --}}
                                        <select class="form-control"
                                                wire:model.live="content.relations.mapping.measure_category"
                                                id="measure-category">
                                            <option value="">
                                                @lang('default.form.dropdown.choose')
                                            </option>
                                            @foreach($measures as $measure)
                                                <option value="{{ $measure->id }}">
                                                    {{ $measure->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endcomponent
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                @component('layouts.parts.components.form-group', ['input_name' => 'content.costs.from'])
                                    <label for="costs-from">
                                        @lang('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.form.costs-from.label')
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="icon-sm icon-moneybag"></i></span>
                                        <input type="text" class="form-control" id="costs-from"
                                               wire:model.live="content.costs.from"
                                               placeholder="@lang('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.form.costs-from.placeholder')">
                                    </div>
                                @endcomponent
                            </div>
                            <div class="col-sm-6">
                                @component('layouts.parts.components.form-group', ['input_name' => 'content.costs.to'])
                                    <label for="costs-to">
                                        @lang('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.form.costs-to.label')
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="icon-sm icon-moneybag"></i></span>
                                        <input type="text" class="form-control" id="costs-to"
                                               wire:model.live="content.costs.to"
                                               placeholder="@lang('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.form.costs-to.placeholder')">
                                    </div>
                                @endcomponent
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                @component('layouts.parts.components.form-group', ['input_name' => 'content.savings_money'])
                                    <label for="savings-money">
                                        @lang('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.form.savings.label')
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="icon-sm icon-moneybag"></i></span>
                                        <input type="text" class="form-control" id="savings-money"
                                               wire:model.live="content.savings_money"
                                               placeholder="@lang('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.form.savings.placeholder')">
                                    </div>
                                @endcomponent
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                @component('layouts.parts.components.form-group', ['input_name' => 'content.extra.icon'])
                                    <label for="icon">
                                        @lang('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.form.icon.label')
                                    </label>
                                    <div wire:ignore>
                                        <select class="form-control" id="icon"
                                                wire:model.live="content.extra.icon">
                                            <option value="">
                                                @lang('default.form.dropdown.choose')
                                            </option>
                                            @foreach(File::allFiles(public_path('icons')) as $file)
                                                @php
                                                    $iconName = "icon-" . str_replace(".{$file->getExtension()}", '', $file->getBasename());
                                                @endphp
                                                <option value="{{ $iconName }}">
                                                    {{ $iconName }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endcomponent
                            </div>
                            <div class="col-sm-6" wire:ignore>
                                <i id="icon-preview" style="margin-top: 2.6rem;"></i>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                @component('layouts.parts.components.form-group', ['input_name' => 'content.is_extensive_measure'])
                                    <div class="checkbox-wrapper mt-10">
                                        <input id="is-extensive-measure" wire:model.live="content.is_extensive_measure"
                                               type="checkbox" value="1">
                                        <label for="is-extensive-measure">
                                            <span class="checkmark"></span>
                                            <span>
                                                @lang('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.form.is-extensive-measure.label')
                                            </span>
                                        </label>
                                    </div>
                                @endcomponent
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('js')
    <script type="module">
        document.addEventListener('DOMContentLoaded', () => {
            var $measureCategory = $('#measure-category');
            $measureCategory.select2();

            $measureCategory.change(function (event) {
                Livewire.dispatchTo('cooperation.admin.super-admin.cooperation-presets.cooperation-preset-contents.cooperation-measure-applications.form', 'fieldUpdate', {field: 'content.relations.mapping.measure_category', value: $(this).val()});
            });

            var $icon = $('#icon');
            $icon.select2();

            $icon.change(function (event) {
                var $iconPreview = $('#icon-preview');
                $iconPreview.removeClass();
                var icon = $(this).val();
                $iconPreview.addClass(`icon-lg ${icon}`);

                // So, select2 triggers a jQuery change, which isn't caught by Livewire. We dispatch to handle it...
                Livewire.dispatchTo('cooperation.admin.super-admin.cooperation-presets.cooperation-preset-contents.cooperation-measure-applications.form', 'fieldUpdate', {field: 'content.extra.icon', value: icon});
            });

            $icon.trigger('change');
        });
    </script>
@endpush