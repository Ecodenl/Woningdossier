<div>
    <form wire:submit.prevent="save()">
        <div class="row">
            <div class="col-sm-6">
                <a id="leave-creation-tool" class="btn btn-warning"
                   href="{{route('cooperation.admin.super-admin.cooperation-presets.show', compact('cooperationPreset'))}}">
                    @lang('woningdossier.cooperation.admin.cooperation.questionnaires.create.leave-creation-tool')
                </a>
            </div>
            <div class="col-sm-6">
                <button type="submit"  class="btn btn-primary pull-right">
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
                                                   wire:model="content.name.{{$locale}}"
                                                   placeholder="@lang('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.form.name.placeholder')">
                                        </div>
                                    @endcomponent
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    @component('layouts.parts.components.form-group', ["content.info.{$locale}"])
                                        <label for="info-{{$locale}}">
                                            @lang('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.form.info.label')
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-addon">{{$locale}}</span>
                                            <textarea class="form-control" id="info-{{$locale}}"
                                                      wire:model="content.info.{{$locale}}"
                                                      placeholder="@lang('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.form.info.placeholder')"
                                            ></textarea>
                                        </div>
                                    @endcomponent
                                </div>
                            </div>
                        @endforeach
                        <div class="row">
                            <div class="col-sm-6">
                                @component('layouts.parts.components.form-group', ['input_name' => 'content.costs.from'])
                                    <label for="costs-from">
                                        @lang('cooperation/admin/cooperation/cooperation-admin/cooperation-measure-applications.form.costs-from.label')
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="icon-sm icon-moneybag"></i></span>
                                        <input type="text" class="form-control" id="costs-from"
                                               wire:model="content.costs.from"
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
                                               wire:model="content.costs.to"
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
                                               wire:model="content.savings_money"
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
                                    <select class="form-control" id="icon"
                                            wire:model="content.extra.icon">
                                        @foreach(File::allFiles(public_path('icons')) as $file)
                                            @php
                                                $iconName = "icon-" . str_replace(".{$file->getExtension()}", '', $file->getBasename());
                                            @endphp
                                            <option value="{{ $iconName }}">
                                                {{ $iconName }}
                                            </option>
                                        @endforeach
                                    </select>
                                @endcomponent
                            </div>
                            <div class="col-sm-6">
                                <i id="icon-preview" style="margin-top: 2.6rem;"></i>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                @component('layouts.parts.components.form-group', ['input_name' => 'content.is_extensive_measure'])
                                    <div class="checkbox-wrapper">
                                        <input id="is-extensive-measure" wire:model="content.is_extensive_measure"
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
    <script>
        $(document).ready(() => {
            var $icon = $('#icon');
            $icon.select2();

            $icon.change(function () {
                var $iconPreview = $('#icon-preview');
                $iconPreview.removeClass();
                var icon = $(this).val();
                $iconPreview.addClass(`icon-lg ${icon}`);
            });

            $icon.trigger('change');
        });
    </script>
@endpush