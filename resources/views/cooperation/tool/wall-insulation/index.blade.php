@extends('cooperation.frontend.layouts.tool')

@section('step_title', \App\Helpers\Translation::translate('wall-insulation.title.title'))

@section('content')
    <form method="POST"
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
                </div>

            </div>
        </div>
        @include('cooperation.tool.includes.savings-alert', ['buildingElement' => 'wall-insulation'])
        <div class="hideable">

            @if(isset($building->buildingFeatures->build_year))
                <div class="flex flex-row flex-wrap w-full">
                    <div class="w-full">
                        <label for="house_has_insulation" class="control-label">
                            {{\App\Helpers\Translation::translate('wall-insulation.intro.build-year.title', ['year' => $building->buildingFeatures->build_year]) }}
                            @if($building->buildingFeatures->build_year >= 1985)
                                {{\App\Helpers\Translation::translate('wall-insulation.intro.build-year-post-1985.title')}}
                            @elseif($building->buildingFeatures->build_year >= 1930)
                                {{\App\Helpers\Translation::translate('wall-insulation.intro.build-year-post-1930.title')}}
                            @else
                                {{\App\Helpers\Translation::translate('wall-insulation.intro.build-year-pre-1930.title')}}
                            @endif
                        </label>
                    </div>
                </div>
            @endif

            <div class="flex flex-row flex-wrap w-full">
                <div class="w-full">

                    @component('cooperation.tool.components.input-group',
                    ['inputType' => 'radio',
                    'inputValues' => [
                        1 => \App\Helpers\Translation::translate('general.options.yes.title'),
                        2 => \App\Helpers\Translation::translate('general.options.no.title'),
                        0 => \App\Helpers\Translation::translate('general.options.unknown.title'),
                    ],
                    'userInputValues' => $buildingFeaturesForMe, 'userInputColumn' => 'cavity_wall'])
                        @component('cooperation.tool.components.step-question', ['id' => 'cavity_wall', 'translation' => 'wall-insulation.intro.has-cavity-wall', 'required' => true])
                            <label class="radio-inline">
                                <input type="radio" name="building_features[cavity_wall]"
                                       @if(old('building_features.cavity_wall', \App\Helpers\Hoomdossier::getMostCredibleValueFromCollection($buildingFeaturesOrderedOnCredibility, 'cavity_wall')) == 1) checked
                                       @endif value="1">{{\App\Helpers\Translation::translate('general.options.yes.title') }}
                                {{--<input type="radio" name="cavity_wall" @if(old('cavity_wall') == "1") checked @elseif(isset($buildingFeature) && $buildingFeature->cavity_wall == "1") checked @endif  value="1">@lang('woningdossier.cooperation.radiobutton.yes')--}}
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="building_features[cavity_wall]"
                                       @if(old('building_features.cavity_wall', \App\Helpers\Hoomdossier::getMostCredibleValueFromCollection($buildingFeaturesOrderedOnCredibility, 'cavity_wall')) == 2) checked
                                       @endif value="2">{{\App\Helpers\Translation::translate('general.options.no.title') }}
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="building_features[cavity_wall]"
                                       @if(old('building_features.cavity_wall', \App\Helpers\Hoomdossier::getMostCredibleValueFromCollection($buildingFeaturesOrderedOnCredibility, 'cavity_wall')) == 0) checked
                                       @endif value="0">{{\App\Helpers\Translation::translate('general.options.unknown.title') }}
                            </label>
                        @endcomponent
                        <br>

                    @endcomponent

                </div>
            </div>
        </div>
        <div class="flex flex-row flex-wrap w-full">
            <div class="w-full">

                @component('cooperation.tool.components.input-group', ['inputType' => 'radio', 'inputValues' => [
                    1 => \App\Helpers\Translation::translate('general.options.yes.title'),
                    2 => \App\Helpers\Translation::translate('general.options.no.title'),
                    3 => \App\Helpers\Translation::translate('general.options.unknown.title'),
                ],
                    'userInputValues' => $buildingFeaturesForMe, 'userInputColumn' => 'facade_plastered_painted'])

                    @component('cooperation.tool.components.step-question', ['id' => 'facade_plastered_painted', 'translation' => 'wall-insulation.intro.is-facade-plastered-painted', 'required' => true])
                        <label class="radio-inline">
                            <input class="is-painted"
                                   @if(old('building_features.facade_plastered_painted', \App\Helpers\Hoomdossier::getMostCredibleValueFromCollection($buildingFeaturesOrderedOnCredibility, 'facade_plastered_painted')) == 1) checked
                                   @endif type="radio" name="building_features[facade_plastered_painted]"
                                   value="1">{{ \App\Helpers\Translation::translate('general.options.yes.title') }}
                        </label>
                        <label class="radio-inline">
                            <input @if(old('building_features.facade_plastered_painted', \App\Helpers\Hoomdossier::getMostCredibleValueFromCollection($buildingFeaturesOrderedOnCredibility, 'facade_plastered_painted')) == 2) checked
                                   @endif type="radio" name="building_features[facade_plastered_painted]"
                                   value="2">{{ \App\Helpers\Translation::translate('general.options.no.title') }}
                        </label>
                        <label class="radio-inline">
                            <input class="is-painted"
                                   @if(old('building_features.facade_plastered_painted', \App\Helpers\Hoomdossier::getMostCredibleValueFromCollection($buildingFeaturesOrderedOnCredibility, 'facade_plastered_painted')) == 3) checked
                                   @endif type="radio" name="building_features[facade_plastered_painted]"
                                   value="3">{{ \App\Helpers\Translation::translate('general.options.unknown.title') }}
                        </label>
                    @endcomponent
                    <br>

                @endcomponent

            </div>
        </div>

        <div class="flex flex-row flex-wrap w-full">
            <div id="painted-options" style="display: none;">
                <div class="col-sm-6">
                    @component('cooperation.tool.components.step-question', ['id' => 'building_features.facade_plastered_surface_id', 'translation' => 'wall-insulation.intro.surface-paintwork', 'required' => false])
                        @component('cooperation.tool.components.input-group',
                        ['inputType' => 'select', 'inputValues' => $facadePlasteredSurfaces, 'userInputValues' => $buildingFeaturesForMe, 'userInputColumn' => 'facade_plastered_surface_id'])
                            <select id="building_features.facade_plastered_surface_id" class="form-control"
                                    name="building_features[facade_plastered_surface_id]">
                                @foreach($facadePlasteredSurfaces as $facadePlasteredSurface)
                                    <option @if(old('building_features.facade_plastered_surface_id', \App\Helpers\Hoomdossier::getMostCredibleValueFromCollection($buildingFeaturesOrderedOnCredibility, 'building_features.facade_plastered_surface_id'))  == $facadePlasteredSurface->id) selected="selected"
                                            @endif value="{{ $facadePlasteredSurface->id }}">{{ $facadePlasteredSurface->name }}</option>
                                    {{--<option @if(old('building_features.facade_plastered_surface_id') == $facadePlasteredSurface->id) selected @elseif(isset($buildingFeature) && $buildingFeature->building_features.facade_plastered_surface_id == $facadePlasteredSurface->id ) selected @endif value="{{ $facadePlasteredSurface->id }}">{{ $facadePlasteredSurface->name }}</option>--}}
                                @endforeach
                            </select>@endcomponent
                    @endcomponent

                </div>

                <div class="col-sm-6">

                    @component('cooperation.tool.components.step-question', ['id' => 'facade_damaged_paintwork_id', 'translation' => 'wall-insulation.intro.damage-paintwork', 'required' => false])
                        @component('cooperation.tool.components.input-group', ['inputType' => 'select', 'inputValues' => $facadeDamages, 'userInputValues' => $buildingFeaturesForMe ,'userInputColumn' => 'facade_damaged_paintwork_id'])
                            <select id="facade_damaged_paintwork_id" class="form-control"
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
        </div>
        <div class="flex flex-row flex-wrap w-full">
            <div class="hideable" id="surfaces">
                <div class="col-sm-6">
                    @component('cooperation.tool.components.step-question', ['id' => 'building_features.wall_surface', 'translation' => 'wall-insulation.optional.facade-surface', 'required' => true])

                        @component('cooperation.tool.components.input-group',
                        ['inputType' => 'input', 'userInputValues' => $buildingFeaturesForMe ,'userInputColumn' => 'wall_surface', 'needsFormat' => true])
                            <input id="wall_surface" type="text" name="building_features[wall_surface]"
                                   value="{{ \App\Helpers\NumberFormatter::format(old('building_features.wall_surface', \App\Helpers\Hoomdossier::getMostCredibleValueFromCollection($buildingFeaturesOrderedOnCredibility, 'wall_surface')),1) }}"
                                   class="form-control" required="required">
                            <span class="input-group-addon">{{\App\Helpers\Translation::translate('general.unit.square-meters.title')}}</span>
                        @endcomponent

                    @endcomponent
                </div>
                <div class="col-sm-6">
                    @component('cooperation.tool.components.step-question', ['id' => 'building_features.insulation_wall_surface', 'translation' => 'wall-insulation.optional.insulated-surface', 'required' => true])

                        @component('cooperation.tool.components.input-group',
                    ['inputType' => 'input', 'userInputValues' => $buildingFeaturesForMe ,'userInputColumn' => 'insulation_wall_surface', 'needsFormat' => true])
                            <input id="insulation_wall_surface" type="text" name="building_features[insulation_wall_surface]" required="required"
                                   value="{{ \App\Helpers\NumberFormatter::format(old('building_features.insulation_wall_surface', \App\Helpers\Hoomdossier::getMostCredibleValueFromCollection($buildingFeaturesOrderedOnCredibility, 'insulation_wall_surface')),1) }}"
                                   class="form-control">
                            <span class="input-group-addon">{{\App\Helpers\Translation::translate('general.unit.square-meters.title')}}</span>
                        @endcomponent

                    @endcomponent
                </div>
            </div>

            <div class="hideable">
                <div id="advice-help">
                    <div class="col-sm-12 col-md-8 col-md-offset-2">
                        <div class="alert alert-info" role="alert">
                            <p>{{\App\Helpers\Translation::translate('wall-insulation.insulation-advice.text.title')}}</p>
                            <p id="insulation-advice"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="options">
            <hr>
            @include('cooperation.tool.includes.section-title', ['translation' => 'wall-insulation.optional.title', 'id' => 'optional',])

            <div id="wall-joints" class="flex flex-row flex-wrap w-full">
                <div class="col-sm-6">

                    @component('cooperation.tool.components.step-question', ['id' => 'building_features.wall_joints', 'translation' => 'wall-insulation.optional.flushing', 'required' => false])
                        @component('cooperation.tool.components.input-group',
                    ['inputType' => 'select', 'inputValues' => $surfaces, 'userInputValues' => $buildingFeaturesForMe ,'userInputColumn' => 'wall_joints'])
                            <select id="building_features.wall_joints" class="form-control" name="building_features[wall_joints]">
                                @foreach($surfaces as $surface)
                                    <option @if(old('building_features.wall_joints', \App\Helpers\Hoomdossier::getMostCredibleValueFromCollection($buildingFeaturesOrderedOnCredibility, 'wall_joints'))  == $surface->id) selected="selected"
                                            @endif value="{{ $surface->id }}">{{ $surface->name }}</option>
                                    {{--<option @if(old('building_features.wall_joints') == $surface->id) selected @elseif(isset($buildingFeature) && $buildingFeature->building_features.wall_joints == $surface->id ) selected  @endif value="{{ $surface->id }}">{{ $surface->name }}</option>--}}
                                @endforeach
                            </select>@endcomponent
                    @endcomponent
                </div>

                <div class="col-sm-6">
                    @component('cooperation.tool.components.step-question', ['id' => 'building_features.contaminated_wall_joints', 'translation' => 'wall-insulation.optional.is-facade-dirty', 'required' => false])
                        @component('cooperation.tool.components.input-group',
                            ['inputType' => 'select', 'inputValues' => $surfaces, 'userInputValues' => $buildingFeaturesForMe ,'userInputColumn' => 'contaminated_wall_joints'])
                            <select id="contaminated_wall_joints" class="form-control" name="building_features[contaminated_wall_joints]">
                                @foreach($surfaces as $surface)
                                    <option @if(old('building_features.contaminated_wall_joints') == $surface->id) selected
                                            @elseif(isset($buildingFeature) && $buildingFeature->contaminated_wall_joints == $surface->id ) selected
                                            @endif value="{{ $surface->id }}">{{ $surface->name }}</option>
                                @endforeach
                            </select>
                        @endcomponent
                    @endcomponent
                </div>

            </div>


            <div class="hideable">
                <div class="flex flex-row flex-wrap w-full" id="cavity-wall-alert" style="display: none;">
                    <div class="col-sm-12 col-md-8 col-md-offset-2">
                        <div class="alert alert-warning" role="alert">
                            <p>
                                <strong>{{ \App\Helpers\Translation::translate('wall-insulation.alerts.description.title') }}</strong>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="hideable">
                <div id="indication-for-costs">
                    <hr>
                    @include('cooperation.tool.includes.section-title', [
                            'translation' => 'wall-insulation.indication-for-costs.title',
                            'id' => 'indication-for-costs'
                        ])

                    <div id="costs" class="flex flex-row flex-wrap w-full">
                        <div class="col-sm-4">
                            @include('cooperation.layouts.indication-for-costs.gas', ['translation' => 'wall-insulation.index.costs.gas'])
                        </div>
                        <div class="col-sm-4">
                            @include('cooperation.layouts.indication-for-costs.co2', ['translation' => 'wall-insulation.index.costs.co2'])
                        </div>
                        <div class="col-sm-4">
                            @include('cooperation.layouts.indication-for-costs.savings-in-euro', [
                                'translation' => 'wall-insulation.index.savings-in-euro'
                             ])
                        </div>
                    </div>
                    <div class="flex flex-row flex-wrap w-full">
                        <div class="col-sm-4">
                            @include('cooperation.layouts.indication-for-costs.indicative-costs', [
                                'translation' => 'wall-insulation.index.indicative-costs'
                             ])
                        </div>
                        <div class="col-sm-4">
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
                <span>{{\App\Helpers\Translation::translate('wall-insulation.taking-into-account.sub-title.title')}}</span>

                <div class="flex flex-row flex-wrap w-full">
                    <div class="col-sm-6">
                        @component('cooperation.tool.components.step-question', ['id' => 'repair_joint', 'translation' => 'wall-insulation.taking-into-account.repair-joint', 'required' => false])
                            <span id="repair_joint_year">(in 2018)</span>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-euro"></i></span>
                                <input type="text" id="repair_joint" class="form-control disabled" disabled=""
                                       value="0">
                            </div>
                        @endcomponent
                    </div>
                    <div class="col-sm-6">
                        @component('cooperation.tool.components.step-question', ['id' => 'clean_brickwork', 'translation' => 'wall-insulation.taking-into-account.clean-brickwork', 'required' => false])
                            <span id="clean_brickwork_year"></span>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-euro"></i></span>
                                <input type="text" id="clean_brickwork" class="form-control disabled" disabled=""
                                       value="0">
                            </div>
                        @endcomponent
                    </div>
                </div>
                <div class="flex flex-row flex-wrap w-full">
                    <div class="col-sm-6">
                        @component('cooperation.tool.components.step-question', ['id' => 'impregnate_wall', 'translation' => 'wall-insulation.taking-into-account.impregnate-wall', 'required' => false])
                            <span id="impregnate_wall_year"></span>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-euro"></i></span>
                                <input type="text" id="impregnate_wall" class="form-control disabled" disabled=""
                                       value="0">
                            </div>
                        @endcomponent
                    </div>
                    <div class="col-sm-6">
                        @component('cooperation.tool.components.step-question', ['id' => 'paint_wall', 'translation' => 'wall-insulation.taking-into-account.wall-painting', 'required' => false])
                            <span id="paint_wall_year"></span>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-euro"></i></span>
                                <input type="text" id="paint_wall" class="form-control disabled" disabled="" value="0">
                            </div>
                        @endcomponent
                    </div>
                </div>

            </div>

            @include('cooperation.tool.includes.comment', [
                 'translation' => 'wall-insulation.index.specific-situation'
            ])

            <div class="flex flex-row flex-wrap w-full">
                <div class="col-md-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">@lang('default.buttons.download')</div>
                        <div class="panel-body">
                            <ol>
                                <li><a download=""
                                       href="{{asset('storage/hoomdossier-assets/Maatregelblad_Gevelisolatie.pdf')}}">{{ ucfirst(strtolower(str_replace(['-', '_'], ' ', basename(asset('storage/hoomdossier-assets/Maatregelblad_Gevelisolatie.pdf'))))) }}</a>
                                </li>
                                <li><a download=""
                                       href="{{asset('storage/hoomdossier-assets/Maatregelblad_Spouwisolatie.pdf')}}">{{ ucfirst(strtolower(str_replace(['-', '_'], ' ', basename(asset('storage/hoomdossier-assets/Maatregelblad_Spouwisolatie.pdf'))))) }}</a>
                                </li>
                            </ol>
                        </div>
                    </div>
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

                var form = $(this).closest("form").serialize();
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

