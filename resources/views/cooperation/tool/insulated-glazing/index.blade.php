@extends('cooperation.tool.layout')

@section('step_title', \App\Helpers\Translation::translate('insulated-glazing.title.title'))


@section('step_content')

    <?php
    // we only need this for the titles above the main inputs
    // we dont want a title above the hr3p-frames
    $titles = [
        7 => 'glass-in-lead',
        8 => 'hrpp-glass-only',
        9 => 'hrpp-glass-frames',
    ];
    ?>
    <form class="form-horizontal" method="POST"
          action="{{ route('cooperation.tool.insulated-glazing.store', ['cooperation' => $cooperation]) }}">
        {{ csrf_field() }}
        @include('cooperation.tool.includes.interested', ['type' => 'element'])
        <div id="main-glass-questions">
            @foreach($measureApplications as $i => $measureApplication)
                @if($i > 0 && array_key_exists($measureApplication->id, $titles))
                    <hr>
                @endif
                <?php
                if (array_key_exists($measureApplication->id, $buildingInsulatedGlazingsForMe)) {
                    $currentMeasureBuildingInsulatedGlazingForMe = $buildingInsulatedGlazingsForMe[$measureApplication->id];
                } else {
                    $currentMeasureBuildingInsulatedGlazingForMe = [];
                }
                ?>

                <div class="row">
                    <div class="col-sm-12">
                        {{-- since there is no title / subtitle for the hr3p --}}
                        @if(array_key_exists($measureApplication->id, $titles))
                            @include('cooperation.tool.includes.section-title', [
                              'translation' => 'insulated-glazing.subtitles.'.$measureApplication->short,
                                'id' => 'insulated-glazing-subtitles-'.$measureApplication->short
                            ])

                        @endif

                        @component('cooperation.tool.components.step-question', 
                        ['id' => 'user_interests.'.$measureApplication->id, 'translation' => 'insulated-glazing.'.$measureApplication->short.'.title', 'required' => false])

                            @component('cooperation.tool.components.input-group',
                            ['inputType' => 'select', 'inputValues' => $interests, 'userInputValues' => $userInterestsForMe->where('interested_in_id', $measureApplication->id),  'userInputColumn' => 'interest_id'])
                                <select id="{{ $measureApplication->id }}" class="user-interest form-control"
                                        name="user_interests[{{ $measureApplication->id }}]">
                                    @foreach($interests as $interest)
                                        {{-- calculate_value 4 is the default --}}
                                        <option data-calculate-value="{{$interest->calculate_value}}"
                                                @if($interest->id == old('user_interests.' . $measureApplication->id) || (array_key_exists($measureApplication->id, $userInterests) && $interest->id == $userInterests[$measureApplication->id]))
                                                selected="selected"
                                                @elseif($buildingOwner->getInterestedType('measure_application', $measureApplication->id) != null && $buildingOwner->getInterestedType('measure_application', $measureApplication->id)->interest_id == $interest->id)
                                                selected="selected"
                                                @elseif(!array_key_exists($measureApplication->id, $userInterests) && $interest->calculate_value == 4)
                                                selected="selected"
                                                @endif
                                                value="{{ $interest->id }}">{{ $interest->name }}
                                        </option>
                                    @endforeach
                                </select>
                            @endcomponent

                        @endcomponent

                    </div>

                    <div class="values">


                        <div class="col-sm-3">
                            @component('cooperation.tool.components.step-question', ['id' => 'building_insulated_glazings.' . $measureApplication->id . '.insulated_glazing_id', 'translation' => 'insulated-glazing.'.$measureApplication->short.'.current-glass', 'required' => false])
                                @component('cooperation.tool.components.input-group',
                                ['inputType' => 'select', 'inputValues' => $insulatedGlazings, 'userInputValues' => $currentMeasureBuildingInsulatedGlazingForMe ,'userInputColumn' => 'insulating_glazing_id'])
                                    <select class="form-control"
                                            name="building_insulated_glazings[{{ $measureApplication->id }}][insulated_glazing_id]">
                                        @foreach($insulatedGlazings as $insulateGlazing)
                                            <option @if($insulateGlazing->id == old('building_insulated_glazings.' . $measureApplication->id . '.insulated_glazing_id', \App\Helpers\Hoomdossier::getMostCredibleValue($building->currentInsulatedGlazing()->where('measure_application_id', $measureApplication->id), 'insulating_glazing_id'))) selected
                                                    @endif value="{{ $insulateGlazing->id }}">{{ $insulateGlazing->name }}</option>
                                            {{--<option @if($insulateGlazing->id == old('building_insulated_glazings.' . $measureApplication->id . '.insulated_glazing_id') || (array_key_exists($measureApplication->id, $buildingInsulatedGlazings) && $buildingInsulatedGlazings[$measureApplication->id]->insulating_glazing_id == $insulateGlazing->id)) selected @endif value="{{ $insulateGlazing->id }}">{{ $insulateGlazing->name }}</option>--}}
                                        @endforeach
                                    </select>
                                @endcomponent

                            @endcomponent

                        </div>
                        <div class="col-sm-3">
                            @component('cooperation.tool.components.step-question',
                            ['id' => 'building_insulated_glazings.' . $measureApplication->id . '.building_heating_id', 'translation' => 'insulated-glazing.'.$measureApplication->short.'.rooms-heated', 'required' => false])

                                @component('cooperation.tool.components.input-group',
                                ['inputType' => 'select', 'inputValues' => $heatings, 'userInputValues' => $currentMeasureBuildingInsulatedGlazingForMe ,'userInputColumn' => 'building_heating_id'])
                                    <select class="form-control"
                                            name="building_insulated_glazings[{{ $measureApplication->id }}][building_heating_id]">
                                        @foreach($heatings as $heating)
                                            <option @if($heating->id == old('building_insulated_glazings.' . $measureApplication->id . '.building_heating_id', \App\Helpers\Hoomdossier::getMostCredibleValue($building->currentInsulatedGlazing()->where('measure_application_id', $measureApplication->id), 'building_heating_id'))) selected
                                                    @endif value="{{ $heating->id }}">{{ $heating->name }}</option>
                                            {{--<option @if($heating->id == old('building_insulated_glazings.' . $measureApplication->id . '.building_heating_id') || (array_key_exists($measureApplication->id, $buildingInsulatedGlazings) && $buildingInsulatedGlazings[$measureApplication->id]->building_heating_id == $heating->id)) selected="selected" @endif value="{{ $heating->id }}">{{ $heating->name }}</option>--}}
                                        @endforeach
                                    </select>
                                @endcomponent

                            @endcomponent

                        </div>
                        <div class="col-sm-3">
                            @component('cooperation.tool.components.step-question',
                             ['id' => 'building_insulated_glazings.' . $measureApplication->id . '.m2', 'translation' => 'insulated-glazing.'.$measureApplication->short.'.m2', 'required' => false])
                                <span> *</span>

                                @component('cooperation.tool.components.input-group',
                                ['inputType' => 'input', 'userInputValues' => $currentMeasureBuildingInsulatedGlazingForMe ,'userInputColumn' => 'm2'])
                                    <input type="text"
                                           name="building_insulated_glazings[{{ $measureApplication->id }}][m2]"
                                           value="{{ old('building_insulated_glazings.' . $measureApplication->id . '.m2', \App\Helpers\Hoomdossier::getMostCredibleValue($building->currentInsulatedGlazing()->where('measure_application_id', $measureApplication->id), 'm2', 0)) }}"
                                           class="form-control">
                                    {{--<input type="text" name="building_insulated_glazings[{{ $measureApplication->id }}][m2]" value="{{ old('building_insulated_glazings.' . $measureApplication->id . '.m2', array_key_exists($measureApplication->id, $buildingInsulatedGlazings) ? $buildingInsulatedGlazings[$measureApplication->id]->m2 : '') }}" class="form-control">--}}
                                @endcomponent

                            @endcomponent

                        </div>

                        <div class="col-sm-3">
                            @component('cooperation.tool.components.step-question',
                            ['id' => 'building_insulated_glazings.' . $measureApplication->id . '.windows', 'translation' => 'insulated-glazing.'.$measureApplication->short.'.window-replace', 'required' => false])
                                <span> *</span>

                                @component('cooperation.tool.components.input-group',
                                ['inputType' => 'input', 'userInputValues' => $currentMeasureBuildingInsulatedGlazingForMe ,'userInputColumn' => 'windows'])
                                    <input type="text"
                                           name="building_insulated_glazings[{{ $measureApplication->id }}][windows]"
                                           value="{{ old('building_insulated_glazings.' . $measureApplication->id . '.windows', \App\Helpers\Hoomdossier::getMostCredibleValue($building->currentInsulatedGlazing()->where('measure_application_id', $measureApplication->id), 'windows', 0)) }}"
                                           class="form-control">
                                    {{--<input type="text" name="building_insulated_glazings[{{ $measureApplication->id }}][windows]" value="{{ old('building_insulated_glazings.' . $measureApplication->id . '.windows', array_key_exists($measureApplication->id, $buildingInsulatedGlazings) ? $buildingInsulatedGlazings[$measureApplication->id]->windows : '') }}" class="form-control">--}}
                                @endcomponent
                            @endcomponent
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <hr>
        <div id="remaining-questions">
            <div class="row">
                <div class="col-sm-12">
                    @include('cooperation.tool.includes.section-title', [
                        'translation' => 'insulated-glazing.cracking-seal',
                        'id' => 'cracking-seal-title'
                    ])
                    @component('cooperation.tool.components.step-question',
                    ['id' => 'building_elements.'.$crackSealing->id.'.crack-sealing', 'translation' => 'insulated-glazing.moving-parts-quality', 'required' => false])

                        @component('cooperation.tool.components.input-group',
                        ['inputType' => 'select', 'inputValues' => $crackSealing->values()->orderBy('order')->get(), 'userInputValues' => $building->getBuildingElementsForMe('crack-sealing'), 'userInputColumn' => 'element_value_id'])
                            <select class="form-control" name="building_elements[{{$crackSealing->id}}][crack-sealing]">
                                @foreach($crackSealing->values()->orderBy('order')->get() as $sealingValue)
                                    <option @if(old('building_elements.crack-sealing', \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingElements()->where('element_id', $crackSealing->id), 'element_value_id')) == $sealingValue->id) selected="selected"
                                            @endif value="{{ $sealingValue->id }}">{{ $sealingValue->value }}</option>
                                    {{--<option @if($sealingValue->id == old('building_elements.crack-sealing') || ($building->getBuildingElement('crack-sealing') instanceof \App\Models\BuildingElement && $building->getBuildingElement('crack-sealing')->element_value_id == $sealingValue->id)) selected @endif value="{{ $sealingValue->id }}">{{ $sealingValue->value }}</option>--}}
                                @endforeach
                            </select>
                        @endcomponent


                    @endcomponent

                </div>
            </div>
        </div>

        <div id="paint-work">
            <div class="row">
                <div class="col-sm-12">
                    <hr>
                    @include('cooperation.tool.includes.section-title', [
                 'translation' => 'insulated-glazing.paint-work.title',
                 'id' => 'paint-work-title'
             ])
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    @component('cooperation.tool.components.step-question', ['id' => 'window_surface', 'translation' => 'insulated-glazing.windows-surface', 'required' => false])

                        @component('cooperation.tool.components.input-group',
                       ['inputType' => 'input', 'userInputValues' => $buildingFeaturesForMe, 'userInputColumn' => 'window_surface'])
                            <span class="input-group-addon">{{\App\Helpers\Translation::translate('general.unit.square-meters.title')}}</span>
                            <input type="text" name="window_surface"
                                   value="{{ old('window_surface', \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingFeatures(), 'window_surface')) }}"
                                   class="form-control">
                        @endcomponent
                    @endcomponent

                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                @component('cooperation.tool.components.step-question', ['id' => 'building_elements.'.$frames->id.'.frames', 'translation' => 'insulated-glazing.paint-work.which-frames', 'required' => false])

                    @component('cooperation.tool.components.input-group',
                    ['inputType' => 'select', 'inputValues' => $frames->values()->orderBy('order')->get(), 'userInputValues' => $building->getBuildingElementsForMe('frames'), 'userInputColumn' => 'element_value_id'])
                        <select class="form-control" name="building_elements[{{$frames->id}}][frames]">
                            @foreach($frames->values()->orderBy('order')->get() as $frameValue)
                                <option @if(old('building_elements.frames', \App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingElements()->where('element_id', $frames->id), 'element_value_id')) == $frameValue->id) selected="selected"
                                        @endif value="{{ $frameValue->id }}">{{ $frameValue->value }}</option>
                                {{--<option @if($frameValue->id == old('building_elements.frames')  || ($building->getBuildingElement('frames') instanceof \App\Models\BuildingElement && $building->getBuildingElement('frames')->element_value_id == $frameValue->id)) selected @endif value="{{ $frameValue->id }}">{{ $frameValue->value }}</option>--}}
                            @endforeach
                        </select>
                    @endcomponent

                @endcomponent

            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                @component('cooperation.tool.components.step-question',
                ['id' => 'building_elements.'.$woodElements->id.'.wood-elements', 'translation' => 'insulated-glazing.paint-work.other-wood-elements', 'required' => false])




                    <?php
                    // TODO: should do something with a component
                    // the current problem is there are only 2 places where checkboxes are used and those are used in a different way
                    ?>
                    <div class="input-group input-source-group">
                        @foreach($woodElements->values()->orderBy('order')->get() as $woodElement)
                            <label for="building_elements.wood-elements.{{ $woodElement->id }}"
                                   class="checkbox-inline">
                                <input
                                        @if(old('building_elements.wood-elements.' . $woodElements->id . '.' . $woodElement->id,\App\Helpers\Hoomdossier::getMostCredibleValue($building->buildingElements()->where('element_id', $woodElements->id)->where('element_value_id', $woodElement->id), 'element_value_id')))
                                        checked="checked"
                                        @endif
                                        type="checkbox" id="building_elements.wood-elements.{{ $woodElement->id }}"
                                        value="{{$woodElement->id}}"
                                        name="building_elements[wood-elements][{{ $woodElements->id }}][{{$woodElement->id}}]">
                                {{ $woodElement->value }}
                            </label>
                        @endforeach
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                <span class="caret"></span></button>
                            <ul class="dropdown-menu">
                                @foreach ($woodElements->values()->orderBy('order')->get() as $woodElement)
                                    <?php
                                    $myWoodElement = $myBuildingElements->where('element_id', $woodElements->id)->where('element_value_id', $woodElement->id)->first();
                                    $notNull = null != $myWoodElement;
                                    ?>
                                    @if ($notNull && $myWoodElement->element_value_id == $woodElement->id)
                                        <li class="change-input-value" data-input-value="{{$woodElement->id}}"
                                            data-input-source-short="{{$myWoodElement->inputSource()->first()->short}}">
                                            <a href="#">{{$myWoodElement->getInputSourceName()}}
                                                : {{$woodElement->value}}</a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endcomponent
            </div>
        </div>

        <div class="row">
            <div class="col-sm-6">
                @component('cooperation.tool.components.step-question',
                ['id' => 'building_paintwork_statuses.last_painted_year', 'translation' => 'insulated-glazing.paint-work.last-paintjob', 'required' => false])

                    @component('cooperation.tool.components.input-group',
                           ['inputType' => 'input', 'userInputValues' => $building->currentPaintworkStatus()->forMe()->get() ,'userInputColumn' => 'last_painted_year'])
                        <span class="input-group-addon">{{\App\Helpers\Translation::translate('general.unit.year.title')}}</span>
                        <input type="text" name="building_paintwork_statuses[last_painted_year]"
                               class="form-control"
                               value="{{ old('building_paintwork_statuses.last_painted_year', \App\Helpers\Hoomdossier::getMostCredibleValue($building->currentPaintworkStatus(), 'last_painted_year')) }}">
                        {{--<input type="text" name="building_paintwork_statuses[last_painted_year]" class="form-control" value="{{ old('building_paintwork_statuses.last_painted_year', $building->currentPaintworkStatus instanceof \App\Models\BuildingPaintworkStatus ? $building->currentPaintworkStatus->last_painted_year : '') }}">--}}
                    @endcomponent
                @endcomponent

            </div>
            <div class="col-sm-6">
                @component('cooperation.tool.components.step-question',
                ['id' => 'building_paintwork_statuses.paintwork_status_id', 'translation' => 'insulated-glazing.paint-work.paint-damage-visible', 'required' => false])

                    @component('cooperation.tool.components.input-group',
                    ['inputType' => 'select', 'inputValues' => $paintworkStatuses, 'userInputValues' => $building->currentPaintworkStatus()->forMe()->get(), 'userInputColumn' => 'paintwork_status_id'])
                        <select class="form-control" name="building_paintwork_statuses[paintwork_status_id]">
                            @foreach($paintworkStatuses as $paintworkStatus)
                                <option @if($paintworkStatus->id == old('building_paintwork_statuses.paintwork_status_id', \App\Helpers\Hoomdossier::getMostCredibleValue($building->currentPaintworkStatus(), 'paintwork_status_id'))) selected
                                        @endif value="{{ $paintworkStatus->id }}">{{ $paintworkStatus->name }}</option>
                                {{--<option @if($paintworkStatus->id == old('building_paintwork_statuses.paintwork_status_id') || ($building->currentPaintworkStatus instanceof \App\Models\BuildingPaintworkStatus && $building->currentPaintworkStatus->paintwork_status_id == $paintworkStatus->id) ) selected @endif value="{{ $paintworkStatus->id }}">{{ $paintworkStatus->name }}</option>--}}
                            @endforeach
                        </select>
                    @endcomponent

                @endcomponent

            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                @component('cooperation.tool.components.step-question', ['id' => 'building_paintwork_statuses.wood_rot_status_id', 'translation' => 'insulated-glazing.paint-work.wood-rot-visible', 'required' => false])

                    @component('cooperation.tool.components.input-group',
                    ['inputType' => 'select', 'inputValues' => $woodRotStatuses, 'userInputValues' => $building->currentPaintworkStatus()->forMe()->get(), 'userInputColumn' => 'wood_rot_status_id'])
                        <select class="form-control" name="building_paintwork_statuses[wood_rot_status_id]">
                            @foreach($woodRotStatuses as $woodRotStatus)
                                <option @if($woodRotStatus->id == old('building_paintwork_statuses.wood_rot_status_id', \App\Helpers\Hoomdossier::getMostCredibleValue($building->currentPaintworkStatus(), 'wood_rot_status_id'))) selected
                                        @endif value="{{ $woodRotStatus->id }}">{{ $woodRotStatus->name }}</option>
                                {{--<option @if($woodRotStatus->id == old('building_paintwork_statuses.wood_rot_status_id') || ($building->currentPaintworkStatus instanceof \App\Models\BuildingPaintworkStatus && $building->currentPaintworkStatus->wood_rot_status_id == $woodRotStatus->id) ) selected @endif value="{{ $woodRotStatus->id }}">{{ $woodRotStatus->name }}</option>--}}
                            @endforeach
                        </select>
                    @endcomponent

                @endcomponent

            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group add-space {{ $errors->has('comments') ? ' has-error' : '' }}">
                    @component('cooperation.tool.components.step-question', ['id' => 'comments', 'translation' => 'insulated-glazing.paint-work.comments-paintwork', 'required' => false])

                        <?php
                        // We do this because we store the comment with every glazing
                        $glazingWithComment = collect($buildingInsulatedGlazings)->where('extra', '!=', null)->first();
                        $comment = !is_null($glazingWithComment) && array_key_exists('comment', $glazingWithComment->extra) ? $glazingWithComment->extra['comment'] : '';
                        ?>

                        <textarea name="comment" id="" class="form-control">{{ $comment }}</textarea>

                    @endcomponent

                </div>
            </div>
            <div class="col-sm-12">
                {{--loop through all the insulated glazings with ALL the input sources--}}
                @foreach ($buildingInsulatedGlazingsForMe as $buildingInsulatedGlazingForMe)
                    <?php $coachInputSource = App\Models\InputSource::findByShort('coach'); ?>
                    @if($buildingInsulatedGlazingForMe->where('input_source_id', $coachInputSource->id)->first() instanceof \App\Models\BuildingInsulatedGlazing && array_key_exists('comment', $buildingInsulatedGlazingForMe->where('input_source_id', $coachInputSource->id)->first()->extra))

                        ({{$coachInputSource->name}})
                        @component('cooperation.tool.components.step-question', ['id' => '', 'translation' => 'general.specific-situation', 'required' => false])

                            <textarea disabled="disabled"
                                      class="disabled form-control">{{$buildingInsulatedGlazingForMe->where('input_source_id', $coachInputSource->id)->first()->extra['comment']}}</textarea>
                        @endcomponent

                        @break
                    @endif
                @endforeach
            </div>
        </div>

        <div id="indication-for-costs">
            <hr>
            @include('cooperation.tool.includes.section-title', ['translation' => 'insulated-glazing.costs.cost-and-benefits', 'id' => 'cost-and-benefits',])

            <div id="costs" class="row">
                <div class="col-sm-4">
                    @include('cooperation.layouts.indication-for-costs.gas', ['step' => $currentStep->slug])
                </div>
                <div class="col-sm-4">
                    @include('cooperation.layouts.indication-for-costs.co2', ['step' => $currentStep->slug])
                </div>
                <div class="col-sm-4">
                    @include('cooperation.layouts.indication-for-costs.savings-in-euro')
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    @include('cooperation.layouts.indication-for-costs.indicative-costs')
                </div>
                <div class="col-sm-4">
                    @include('cooperation.layouts.indication-for-costs.comparable-rent')
                </div>
            </div>
        </div>

        <div id="taking-into-account">
            <hr>
            @include('cooperation.tool.includes.section-title', ['translation' => 'insulated-glazing.taking-into-account.title', 'id' => 'taking-into-account',])

            <div class="row">
                <div class="col-sm-6">
                    @component('cooperation.tool.components.step-question', ['id' => 'paintwork', 'translation' => 'insulated-glazing.taking-into-account.paintwork',])
                        <div class="form-group add-space">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-euro"></i></span>
                                <input type="text" id="paintwork_costs" class="form-control disabled" disabled=""
                                       value="0">
                            </div>
                        </div>
                    @endcomponent
                </div>
                <div class="col-sm-6">
                    @component('cooperation.tool.components.step-question', ['id' => 'paintwork_year', 'translation' => 'insulated-glazing.taking-into-account.paintwork_year',])
                        <div class="input-group">
                            <span class="input-group-addon">{{\App\Helpers\Translation::translate('general.unit.year.title')}}</span>
                            <input type="text" id="paintwork_year" class="form-control disabled" disabled=""
                                   value="0">
                        </div>
                    @endcomponent
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">@lang('default.buttons.download')</div>
                    <div class="panel-body">
                        <ol>
                            <li><a download=""
                                   href="{{ asset('storage/hoomdossier-assets/Maatregelblad_Glasisolatie.pdf') }}">{{ucfirst(strtolower(str_replace(['-', '_'], ' ', basename(asset('storage/hoomdossier-assets/Maatregelblad_Glasisolatie.pdf')))))}}</a>
                            </li>
                            <li><a download=""
                                   href="{{ asset('storage/hoomdossier-assets/Maatregelblad_Kierdichting_bouwdelen.pdf') }}">{{ucfirst(strtolower(str_replace(['-', '_'], ' ', basename(asset('storage/hoomdossier-assets/Maatregelblad_Kierdichting_bouwdelen.pdf')))))}}</a>
                            </li>
                            <li><a download=""
                                   href="{{ asset('storage/hoomdossier-assets/Maatregelblad_Kierdichting_ramen_en_deuren.pdf') }}">{{ucfirst(strtolower(str_replace(['-', '_'], ' ', basename(asset('storage/hoomdossier-assets/Maatregelblad_Kierdichting_ramen_en_deuren.pdf')))))}}</a>
                            </li>

                        </ol>
                    </div>
                </div>
                <hr>
                @if(!\App\helpers\HoomdossierSession::isUserObserving())
                <div class="form-group add-space">
                    <div class="">
                        <a class="btn btn-success pull-left"
                           href="{{ route('cooperation.tool.index', [ 'cooperation' => $cooperation ]) }}">@lang('default.buttons.prev')</a>
                        <button type="submit" class="btn btn-primary pull-right">
                            @lang('default.buttons.next')
                        </button>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </form>

@endsection

@push('js')
    <script>
        $(document).ready(function () {


            $("select, input[type=radio], input[type=text], input[type=checkbox]").change(function () {

                var form = $(this).closest("form").serialize();
                $.ajax({
                    type: "POST",
                    url: '{{ route('cooperation.tool.insulated-glazing.calculate', [ 'cooperation' => $cooperation ]) }}',
                    data: form,
                    success: function (data) {

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
                            $("input#interest_comparable").val(data.interest_comparable);
                        }
                        if (data.hasOwnProperty('paintwork')) {
                            $("input#paintwork_costs").val(hoomdossierRound(data.paintwork.costs));
                        }
                        if (data.hasOwnProperty('paintwork')) {
                            $("input#paintwork_year").val(data.paintwork.year);
                        }

                        @if(App::environment('local'))
                        console.log(data);
                        @endif

                        changeOnInterests();
                    }
                });
            });

            function changeOnInterests() {
                $('.user-interest').each(function (i, element) {
                    // the input field
                    var userInterest = $(element);
                    // the user interest calculate value
                    var userInterestCalculateValue = userInterest.find('option:selected').data('calculate-value');

                    if (userInterestCalculateValue === 4 || userInterestCalculateValue === 5) {
                        $(this).parent().parent().parent().parent().find('.values').hide();
                    } else {
                        $(this).parent().parent().parent().parent().find('.values').show();
                    }
                });
            }

            // Trigger the change event so it will load the data
            //$('form').find('*').filter(':input:visible:first').trigger('change');
            $('.panel-body form').find('*').filter(':input:visible:first').trigger('change');
        });

    </script>
@endpush