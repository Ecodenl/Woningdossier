@extends('cooperation.frontend.layouts.tool')

@section('step_title', __('wall-insulation.title.title'))

@section('content')
    <form method="POST" id="wall-insulation-form"
          action="{{ route('cooperation.tool.wall-insulation.store', ['cooperation' => $cooperation]) }}">
        @csrf

        <div id="intro">
            @include('cooperation.tool.includes.interested', [
                'translation' => 'wall-insulation.index.interested-in-improvement', 
                'interestedInType' => \App\Models\Step::class, 'interestedInId' => $currentStep->id,
            ])
            <div class="flex flex-row flex-wrap w-full">
                <div class="w-full">
                    <?php // todo: something seems off with the name ?>
                    @component('cooperation.tool.components.step-question', [
                        'id' => 'element_' . $facadeInsulation->element->id,
                        'name' => 'house_has_insulation',
                        'translation' => 'wall-insulation.intro.filled-insulation'
                    ])
                        @slot('sourceSlot')
                            @include('cooperation.tool.components.source-list', [
                                'inputType' => 'select',
                                'inputValues' => $facadeInsulation->element->values()->orderBy('order')->get(),
                                'userInputValues' => $facadeInsulation->forMe()->get(),
                                'userInputColumn' => 'element_value_id'
                            ])
                        @endslot

                        @component('cooperation.frontend.layouts.components.alpine-select')
                            <select id="element_{{ $facadeInsulation->element->id }}" class="form-input"
                                    name="element[{{ $facadeInsulation->element->id }}]">
                                @foreach($facadeInsulation->element->values()->orderBy('order')->get() as $elementValue)
                                    <option data-calculate-value="{{$elementValue->calculate_value}}"
                                            value="{{ $elementValue->id }}"
                                            @if(old('element.' . $facadeInsulation->element->id, \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingElements()->where('element_id', $facadeInsulation->element->id), 'element_value_id')) == $elementValue->id) selected="selected" @endif>
                                        {{ $elementValue->value }}
                                    </option>
                                @endforeach
                            </select>
                        @endcomponent
                    @endcomponent
                </div>

            </div>
        </div>
        @include('cooperation.tool.includes.savings-alert', ['buildingElement' => 'wall-insulation'])
        <div class="hideable">

            @if(isset($building->buildingFeatures->build_year))
                <div class="flex flex-row flex-wrap w-full">
                    <div class="w-full">
                        <label for="house_has_insulation" class="text-sm text-blue-500 font-bold">
                            @lang('wall-insulation.intro.build-year.title', ['year' => $building->buildingFeatures->build_year])
                            @if($building->buildingFeatures->build_year >= 1985)
                                @lang('wall-insulation.intro.build-year-post-1985.title')
                            @elseif($building->buildingFeatures->build_year >= 1930)
                                @lang('wall-insulation.intro.build-year-post-1930.title')
                            @else
                                @lang('wall-insulation.intro.build-year-pre-1930.title')
                            @endif
                        </label>
                    </div>
                </div>
            @endif

            <div class="flex flex-row flex-wrap w-full">
                <div class="w-full">

                    @component('cooperation.tool.components.step-question', [
                        'id' => 'cavity_wall', 'translation' => 'wall-insulation.intro.has-cavity-wall',
                        'required' => true,
                    ])
                        @slot('sourceSlot')
                            @include('cooperation.tool.components.source-list', [
                                'inputType' => 'radio',
                                'inputValues' => [
                                    1 => __('general.options.yes.title'),
                                    2 => __('general.options.no.title'),
                                    0 => __('general.options.unknown.title'),
                                ],
                                'userInputValues' => $buildingFeaturesForMe, 'userInputColumn' => 'cavity_wall'
                            ])
                        @endslot

                        <div class="radio-wrapper pr-3">
                            <input type="radio" id="building-features-cavity-wall-1"
                                   name="building_features[cavity_wall]" value="1"
                                   @if(old('building_features.cavity_wall', \App\Helpers\Hoomdossier::getMostCredibleValueFromCollection($buildingFeaturesOrderedOnCredibility, 'cavity_wall')) == 1) checked @endif>
                            <label for="building-features-cavity-wall-1">
                                <span class="checkmark"></span>
                                <span>@lang('general.options.yes.title')</span>
                            </label>
                        </div>

                        <div class="radio-wrapper pl-3">
                            <input type="radio" id="building-features-cavity-wall-2"
                                   name="building_features[cavity_wall]" value="2"
                                   @if(old('building_features.cavity_wall', \App\Helpers\Hoomdossier::getMostCredibleValueFromCollection($buildingFeaturesOrderedOnCredibility, 'cavity_wall')) == 2) checked @endif>
                            <label for="building-features-cavity-wall-2">
                                <span class="checkmark"></span>
                                <span>@lang('general.options.no.title')</span>
                            </label>
                        </div>

                        <div class="radio-wrapper pr-3">
                            <input type="radio" id="building-features-cavity-wall-0"
                                   name="building_features[cavity_wall]" value="0"
                                   @if(old('building_features.cavity_wall', \App\Helpers\Hoomdossier::getMostCredibleValueFromCollection($buildingFeaturesOrderedOnCredibility, 'cavity_wall')) == 0) checked @endif>
                            <label for="building-features-cavity-wall-0">
                                <span class="checkmark"></span>
                                <span>@lang('general.options.unknown.title')</span>
                            </label>
                        </div>
                    @endcomponent

                </div>
            </div>
        </div>
        <div class="flex flex-row flex-wrap w-full">
            <div class="w-full">

                @component('cooperation.tool.components.step-question', [
                    'id' => 'facade_plastered_painted',
                    'translation' => 'wall-insulation.intro.is-facade-plastered-painted', 'required' => true
                ])
                    @slot('sourceSlot')
                        @include('cooperation.tool.components.source-list', [
                            'inputType' => 'radio',
                            'inputValues' => [
                                1 => __('general.options.yes.title'),
                                2 => __('general.options.no.title'),
                                3 => __('general.options.unknown.title'),
                            ],
                            'userInputValues' => $buildingFeaturesForMe, 'userInputColumn' => 'facade_plastered_painted'
                        ])
                    @endslot

                    <div class="radio-wrapper pr-3">
                        <input type="radio" id="building-features-facade-plastered-painted-1" class="is-painted"
                               name="building_features[facade_plastered_painted]" value="1"
                               @if(old('building_features.facade_plastered_painted', \App\Helpers\Hoomdossier::getMostCredibleValueFromCollection($buildingFeaturesOrderedOnCredibility, 'facade_plastered_painted')) == 1) checked @endif>
                        <label for="building-features-facade-plastered-painted-1">
                            <span class="checkmark"></span>
                            <span>@lang('general.options.yes.title')</span>
                        </label>
                    </div>
                    <div class="radio-wrapper pl-3">
                        <input type="radio" id="building-features-facade-plastered-painted-2"
                               name="building_features[facade_plastered_painted]" value="2"
                               @if(old('building_features.facade_plastered_painted', \App\Helpers\Hoomdossier::getMostCredibleValueFromCollection($buildingFeaturesOrderedOnCredibility, 'facade_plastered_painted')) == 2) checked @endif>
                        <label for="building-features-facade-plastered-painted-2">
                            <span class="checkmark"></span>
                            <span>@lang('general.options.no.title')</span>
                        </label>
                    </div>
                    <div class="radio-wrapper pr-3">
                        <input type="radio" id="building-features-facade-plastered-painted-3" class="is-painted"
                               name="building_features[facade_plastered_painted]" value="3"
                               @if(old('building_features.facade_plastered_painted', \App\Helpers\Hoomdossier::getMostCredibleValueFromCollection($buildingFeaturesOrderedOnCredibility, 'facade_plastered_painted')) == 3) checked @endif>
                        <label for="building-features-facade-plastered-painted-3">
                            <span class="checkmark"></span>
                            <span>@lang('general.options.unknown.title')</span>
                        </label>
                    </div>
                @endcomponent

            </div>
        </div>

        <div class="flex flex-row flex-wrap w-full" id="painted-options">
            <div class="w-full sm:w-1/2 sm:pr-3">
                @component('cooperation.tool.components.step-question', [
                    'id' => 'building_features.facade_plastered_surface_id',
                    'translation' => 'wall-insulation.intro.surface-paintwork', 'required' => false
                ])
                    @slot('sourceSlot')
                        @include('cooperation.tool.components.source-list',[
                            'inputType' => 'select', 'inputValues' => $facadePlasteredSurfaces,
                            'userInputValues' => $buildingFeaturesForMe,
                            'userInputColumn' => 'facade_plastered_surface_id'
                        ])
                    @endslot

                    @component('cooperation.frontend.layouts.components.alpine-select')
                        <select id="building_features.facade_plastered_surface_id" class="form-input"
                                name="building_features[facade_plastered_surface_id]">
                            @foreach($facadePlasteredSurfaces as $facadePlasteredSurface)
                                <option @if(old('building_features.facade_plastered_surface_id', \App\Helpers\Hoomdossier::getMostCredibleValueFromCollection($buildingFeaturesOrderedOnCredibility, 'building_features.facade_plastered_surface_id'))  == $facadePlasteredSurface->id) selected="selected"
                                        @endif value="{{ $facadePlasteredSurface->id }}">
                                    {{ $facadePlasteredSurface->name }}
                                </option>
                                {{--<option @if(old('building_features.facade_plastered_surface_id') == $facadePlasteredSurface->id) selected @elseif(isset($buildingFeature) && $buildingFeature->building_features.facade_plastered_surface_id == $facadePlasteredSurface->id ) selected @endif value="{{ $facadePlasteredSurface->id }}">{{ $facadePlasteredSurface->name }}</option>--}}
                            @endforeach
                        </select>
                    @endcomponent
                @endcomponent

            </div>

            <div class="w-full sm:w-1/2 sm:pl-3">

                @component('cooperation.tool.components.step-question', ['id' => 'facade_damaged_paintwork_id', 'translation' => 'wall-insulation.intro.damage-paintwork', 'required' => false])
                    @slot('sourceSlot')
                        @include('cooperation.tool.components.source-list', [
                            'inputType' => 'select', 'inputValues' => $facadeDamages,
                            'userInputValues' => $buildingFeaturesForMe,
                            'userInputColumn' => 'facade_damaged_paintwork_id'
                        ])
                    @endslot

                    @component('cooperation.frontend.layouts.components.alpine-select')
                        <select id="facade_damaged_paintwork_id" class="form-input"
                                name="building_features[facade_damaged_paintwork_id]">
                            @foreach($facadeDamages as $facadeDamage)
                                <option @if(old('building_features.facade_damaged_paintwork_id', \App\Helpers\Hoomdossier::getMostCredibleValueFromCollection($buildingFeaturesOrderedOnCredibility, 'facade_damaged_paintwork_id'))  == $facadeDamage->id) selected="selected"
                                        @endif value="{{ $facadeDamage->id }}">{{ $facadeDamage->name }}</option>
                                {{--<option @if(old('facade_damaged_paintwork_id') == $facadeDamage->id) selected @elseif(isset($buildingFeature) && $buildingFeature->facade_damaged_paintwork_id == $facadeDamage->id ) selected  @endif value="{{ $facadeDamage->id }}">{{ $facadeDamage->name }}</option>--}}
                            @endforeach
                        </select>
                    @endcomponent
                @endcomponent

            </div>
        </div>

        <div class="flex flex-row flex-wrap w-full">
            <div class="hideable flex flex-row flex-wrap w-full" id="surfaces">
                <div class="w-full sm:w-1/2 sm:pr-3">
                    @component('cooperation.tool.components.step-question', [
                        'id' => 'building_features.wall_surface',
                        'translation' => 'wall-insulation.optional.facade-surface', 'required' => true,
                    ])
                        @slot('sourceSlot')
                            @include('cooperation.tool.components.source-list', [
                                'inputType' => 'input', 'userInputValues' => $buildingFeaturesForMe,
                                'userInputColumn' => 'wall_surface', 'needsFormat' => true
                            ])
                        @endslot

                        <input id="wall_surface" type="text" name="building_features[wall_surface]"
                               value="{{ \App\Helpers\NumberFormatter::format(old('building_features.wall_surface', \App\Helpers\Hoomdossier::getMostCredibleValueFromCollection($buildingFeaturesOrderedOnCredibility, 'wall_surface')),1) }}"
                               class="form-input with-append" required="required">
                        <span class="input-group-append">@lang('general.unit.square-meters.title')</span>
                    @endcomponent
                </div>
                <div class="w-full sm:w-1/2 sm:pl-3">
                    @component('cooperation.tool.components.step-question', [
                        'id' => 'building_features.insulation_wall_surface',
                        'translation' => 'wall-insulation.optional.insulated-surface', 'required' => true
                    ])
                        @slot('sourceSlot')
                            @include('cooperation.tool.components.source-list', [
                                'inputType' => 'input', 'userInputValues' => $buildingFeaturesForMe,
                                'userInputColumn' => 'insulation_wall_surface', 'needsFormat' => true
                            ])
                        @endslot

                        <input id="insulation_wall_surface" type="text" name="building_features[insulation_wall_surface]" required="required"
                               value="{{ \App\Helpers\NumberFormatter::format(old('building_features.insulation_wall_surface', \App\Helpers\Hoomdossier::getMostCredibleValueFromCollection($buildingFeaturesOrderedOnCredibility, 'insulation_wall_surface')),1) }}"
                               class="form-input with-append">
                        <span class="input-group-append">@lang('general.unit.square-meters.title')</span>
                    @endcomponent
                </div>
            </div>

            <div class="hideable w-full">
                <div id="advice-help">
                    <div class="w-full md:w-2/3 md:ml-2/12">
                        @component('cooperation.frontend.layouts.parts.alert', ['color' => 'blue-800', 'dismissible' => false])
                            <p>@lang('wall-insulation.insulation-advice.text.title')</p>
                            <p id="insulation-advice"></p>
                        @endcomponent
                    </div>
                </div>
            </div>
        </div>

        <div id="options">
            <hr>
            @include('cooperation.tool.includes.section-title', ['translation' => 'wall-insulation.optional.title', 'id' => 'optional',])

            <div id="wall-joints" class="flex flex-row flex-wrap w-full">
                <div class="w-full sm:w-1/2 sm:pr-3">
                    @component('cooperation.tool.components.step-question', [
                        'id' => 'building_features.wall_joints', 'translation' => 'wall-insulation.optional.flushing',
                        'required' => false,
                    ])
                        @slot('sourceSlot')
                            @include('cooperation.tool.components.source-list', [
                                'inputType' => 'select', 'inputValues' => $surfaces,
                                'userInputValues' => $buildingFeaturesForMe ,'userInputColumn' => 'wall_joints'
                            ])
                        @endslot

                        @component('cooperation.frontend.layouts.components.alpine-select')
                            <select id="building_features.wall_joints" class="form-input"
                                    name="building_features[wall_joints]">
                                @foreach($surfaces as $surface)
                                    <option @if(old('building_features.wall_joints', \App\Helpers\Hoomdossier::getMostCredibleValueFromCollection($buildingFeaturesOrderedOnCredibility, 'wall_joints'))  == $surface->id) selected="selected"
                                            @endif value="{{ $surface->id }}">
                                        {{ $surface->name }}
                                    </option>
                                    {{--<option @if(old('building_features.wall_joints') == $surface->id) selected @elseif(isset($buildingFeature) && $buildingFeature->building_features.wall_joints == $surface->id ) selected  @endif value="{{ $surface->id }}">{{ $surface->name }}</option>--}}
                                @endforeach
                            </select>
                        @endcomponent
                    @endcomponent
                </div>

                <div class="w-full sm:w-1/2 sm:pl-3">
                    @component('cooperation.tool.components.step-question', [
                        'id' => 'building_features.contaminated_wall_joints',
                        'translation' => 'wall-insulation.optional.is-facade-dirty', 'required' => false
                    ])
                        @slot('sourceSlot')
                            @include('cooperation.tool.components.source-list', [
                                'inputType' => 'select', 'inputValues' => $surfaces,
                                'userInputValues' => $buildingFeaturesForMe,
                                'userInputColumn' => 'contaminated_wall_joints'
                            ])
                        @endslot

                        @component('cooperation.frontend.layouts.components.alpine-select')
                            <select id="contaminated_wall_joints" class="form-input"
                                    name="building_features[contaminated_wall_joints]">
                                @foreach($surfaces as $surface)
                                    <option @if(old('building_features.contaminated_wall_joints') == $surface->id) selected
                                            @elseif(isset($buildingFeature) && $buildingFeature->contaminated_wall_joints == $surface->id ) selected
                                            @endif value="{{ $surface->id }}">
                                        {{ $surface->name }}
                                    </option>
                                @endforeach
                            </select>
                        @endcomponent
                    @endcomponent
                </div>
            </div>

            <div class="hideable w-full">
                <div class="flex flex-row flex-wrap w-full" id="cavity-wall-alert" style="display: none;">
                    <div class="w-full md:w-2/3 md:ml-2/12">
                        @component('cooperation.frontend.layouts.parts.alert', ['color' => 'red', 'dismissible' => false])
                            <p>
                                <strong>@lang('wall-insulation.alerts.description.title')</strong>
                            </p>
                        @endcomponent
                    </div>
                </div>
            </div>

            <div class="hideable w-full">
                <div id="indication-for-costs">
                    <hr>
                    @include('cooperation.tool.includes.section-title', [
                            'translation' => 'wall-insulation.indication-for-costs.title',
                            'id' => 'indication-for-costs'
                        ])

                    <div id="costs" class="flex flex-row flex-wrap w-full sm:pad-x-6">
                        <div class="w-full sm:w-1/3">
                            @include('cooperation.layouts.indication-for-costs.gas', [
                                'translation' => 'wall-insulation.index.costs.gas'
                            ])
                        </div>
                        <div class="w-full sm:w-1/3">
                            @include('cooperation.layouts.indication-for-costs.co2', [
                                'translation' => 'wall-insulation.index.costs.co2'
                            ])
                        </div>
                        <div class="w-full sm:w-1/3">
                            @include('cooperation.layouts.indication-for-costs.savings-in-euro', [
                                'translation' => 'wall-insulation.index.savings-in-euro'
                             ])
                        </div>
                    </div>
                    <div class="flex flex-row flex-wrap w-full sm:pad-x-6">
                        <div class="w-full sm:w-1/3">
                            @include('cooperation.layouts.indication-for-costs.indicative-costs', [
                                'translation' => 'wall-insulation.index.indicative-costs'
                             ])
                        </div>
                        <div class="w-full sm:w-1/3">
                            @include('cooperation.layouts.indication-for-costs.comparable-rent', [
                                'translation' => 'wall-insulation.index.comparable-rent'
                             ])
                        </div>
                    </div>
                </div>
            </div>
            <div id="taking-into-account">
                <hr>
                @include('cooperation.tool.includes.section-title', ['translation' => 'wall-insulation.taking-into-account.title', 'id' => 'taking-into-account'])
                <span>@lang('wall-insulation.taking-into-account.sub-title.title')</span>

                <div class="flex flex-row flex-wrap w-full">
                    <div class="w-full sm:w-1/2 sm:pr-3">
                        @component('cooperation.tool.components.step-question', [
                            'id' => 'repair_joint', 'translation' => 'wall-insulation.taking-into-account.repair-joint',
                            'required' => false, 'label' => '<span id="repair_joint_year">(in 2018)</span>',
                        ])
                            <span class="input-group-prepend"><i class="icon-sm icon-moneybag"></i></span>
                            <input type="text" id="repair_joint" class="form-input disabled" disabled=""
                                   value="0">
                        @endcomponent
                    </div>
                    <div class="w-full sm:w-1/2 sm:pl-3">
                        @component('cooperation.tool.components.step-question', [
                            'id' => 'clean_brickwork',
                            'translation' => 'wall-insulation.taking-into-account.clean-brickwork', 'required' => false,
                            'label' => '<span id="clean_brickwork_year"></span>',
                        ])
                            <span class="input-group-prepend"><i class="icon-sm icon-moneybag"></i></span>
                            <input type="text" id="clean_brickwork" class="form-input disabled" disabled=""
                            value="0">
                        @endcomponent
                    </div>
                </div>
                <div class="flex flex-row flex-wrap w-full">
                    <div class="w-full sm:w-1/2 sm:pr-3">
                        @component('cooperation.tool.components.step-question', [
                            'id' => 'impregnate_wall',
                            'translation' => 'wall-insulation.taking-into-account.impregnate-wall', 'required' => false,
                            'label' => '<span id="impregnate_wall_year"></span>',
                        ])
                            <span class="input-group-prepend"><i class="icon-sm icon-moneybag"></i></span>
                            <input type="text" id="impregnate_wall" class="form-input disabled" disabled=""
                                   value="0">
                        @endcomponent
                    </div>
                    <div class="w-full sm:w-1/2 sm:pl-3">
                        @component('cooperation.tool.components.step-question', [
                            'id' => 'paint_wall',
                            'translation' => 'wall-insulation.taking-into-account.wall-painting', 'required' => false,
                            'label' => '<span id="paint_wall_year"></span>',
                        ])
                            <span class="input-group-prepend"><i class="icon-sm icon-moneybag"></i></span>
                            <input type="text" id="paint_wall" class="form-input disabled" disabled="" value="0">
                        @endcomponent
                    </div>
                </div>

            </div>

            @include('cooperation.tool.includes.comment', [
                 'translation' => 'wall-insulation.index.specific-situation'
            ])

            <div class="flex flex-row flex-wrap w-full border border-solid border-green rounded-lg">
                <div class="flex flex-row flex-wrap w-full items-center bg-green text-white h-11 px-5 rounded-lg">
                    @lang('default.buttons.download')
                </div>
                <div class="flex flex-row flex-wrap w-full items-center bg-white px-5 py-8 rounded-b-lg">
                    <ol class="list-decimal ml-8">
                        <li><a download=""
                               href="{{asset('storage/hoomdossier-assets/Maatregelblad_Gevelisolatie.pdf')}}">
                                {{ ucfirst(strtolower(str_replace(['-', '_'], ' ', basename(asset('storage/hoomdossier-assets/Maatregelblad_Gevelisolatie.pdf'))))) }}
                            </a>
                        </li>
                        <li><a download=""
                               href="{{asset('storage/hoomdossier-assets/Maatregelblad_Spouwisolatie.pdf')}}">
                                {{ ucfirst(strtolower(str_replace(['-', '_'], ' ', basename(asset('storage/hoomdossier-assets/Maatregelblad_Spouwisolatie.pdf'))))) }}
                            </a>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('js')
    <script>
        $(document).ready(function () {

            $("select, input[type=radio], input[type=text]").change(function () {
                if ($('.is-painted').is(':checked')) {
                    $('#painted-options').show();
                    $('#surfaces').show()
                } else {
                    $('#painted-options').hide();
                    // $('#surfaces').hide()
                }

                let $form = $('#wall-insulation-form');
                let form = $form.serialize();
                $.ajax({
                    type: "POST",
                    url: '{{ route('cooperation.tool.wall-insulation.calculate', [ 'cooperation' => $cooperation ]) }}',
                    data: form,
                    success: function (data) {

                        if (data.hasOwnProperty('insulation_advice')) {
                            $("#insulation-advice").html("<strong>" + data.insulation_advice + "</strong>");

                            // If the advice is spouwmuurisolatie and the walls are painted give them a alert
                            if ((data.insulation_advice == "Spouwmuurisolatie") && ($('.is-painted').is(':checked') == true)) {
                                // Show the alert
                                $('#cavity-wall-alert').show();

                                // Hide the advice
                                $("#advice-help").hide();
                                // Hide the indications and measures
                                // $('#taking-into-account').hide();
                                // $('#indication-for-costs').hide();
                            } else {
                                // hide the alert
                                $('#cavity-wall-alert').hide();

                                // Show the advice
                                $("#advice-help").show();
                                // $('#taking-into-account').show();
                                // $('#indication-for-costs').show();
                            }

                        } else {
                            $("#insulation-advice").html("");
                        }
                        if (data.hasOwnProperty('savings_gas')) {
                            $("input#savings_gas").val(hoomdossierRound(data.savings_gas));
                        }
                        if (data.hasOwnProperty('savings_co2')) {
                            $("input#savings_co2").val(hoomdossierRound(data.savings_co2));
                        }
                        if (data.hasOwnProperty('savings_money')) {
                            $("input#savings_money").val(hoomdossierRound(data.savings_money));
                        }
                        if (data.hasOwnProperty('cost_indication')) {
                            $("input#cost_indication").val(hoomdossierRound(data.cost_indication));
                        }
                        if (data.hasOwnProperty('interest_comparable')) {
                            $("input#interest_comparable").val(hoomdossierNumberFormat(data.interest_comparable, '{{ app()->getLocale() }}', 1));
                        }
                        if (data.hasOwnProperty('repair_joint')) {
                            $("input#repair_joint").val(hoomdossierRound(data.repair_joint.costs));
                            var contentYear = "";
                            if (data.repair_joint.year > 0) {
                                contentYear = "(in " + data.repair_joint.year + ")";
                            }
                            $("span#repair_joint_year").html(contentYear);
                        }
                        if (data.hasOwnProperty('clean_brickwork')) {
                            $("input#clean_brickwork").val(hoomdossierRound(data.clean_brickwork.costs));
                            var contentYear = "";
                            if (data.clean_brickwork.year > 0) {
                                contentYear = "(in " + data.clean_brickwork.year + ")";
                            }
                            $("span#clean_brickwork_year").html(contentYear);
                        }
                        if (data.hasOwnProperty('impregnate_wall')) {
                            $("input#impregnate_wall").val(hoomdossierRound(data.impregnate_wall.costs));
                            var contentYear = "";
                            if (data.impregnate_wall.year > 0) {
                                contentYear = "(in " + data.impregnate_wall.year + ")";
                            }
                            $("span#impregnate_wall_year").html(contentYear);
                        }
                        if (data.hasOwnProperty('paint_wall')) {
                            $("input#paint_wall").val(hoomdossierRound(data.paint_wall.costs));
                            var contentYear = "";
                            if (data.paint_wall.year > 0) {
                                contentYear = "(in " + data.paint_wall.year + ")";
                            }
                            $("span#paint_wall_year").html(contentYear);

                        }

                        checkInterestAndCurrentInsulation();

                        @if(App::environment('local'))
                        console.log(data);
                        @endif
                    }
                })
            });
            // Trigger the change event so it will load the data
            $('.panel-body form').find('*').filter(':input:visible:first').trigger('change');

        });

        $('#wall_surface').on('change', function () {
            if ($('#insulation_wall_surface').val().length == 0 || $('#insulation_wall_surface').val() == "0,0" || $('#insulation_wall_surface').val() == "0.00") {
                $('#insulation_wall_surface').val($('#wall_surface').val())
            }
        });

        function checkInterestAndCurrentInsulation() {
            var elementCalculateValue = $('#element_{{$buildingElements->id}} option:selected').data('calculate-value');

            if (elementCalculateValue >= 3) {
                $('.hideable').hide();
                $('#wall-insulation-info-alert').find('.alert').show();
            } else {
                $('.hideable').show();
                $('#wall-insulation-info-alert').find('.alert').hide();
            }
        }

    </script>
@endpush

