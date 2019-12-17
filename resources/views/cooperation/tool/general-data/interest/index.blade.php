@extends('cooperation.tool.layout')

@push('css')
    <link rel="stylesheet" href="{{asset('css/select2/select2.min.css')}}">
@endpush

@section('step_content')

    <form  method="POST" id="main-form"
          action="{{ route('cooperation.tool.general-data.interest.store') }}" autocomplete="off">
        {{ csrf_field() }}
        <div class="row">
            @for($i = 0; $i < $stepCount; $i++)
                <?php
                $serviceStepShort = key($servicesStepShorts);
                $elementStepShort = key($elementsStepShorts);

                unset($elementsStepShorts[$elementStepShort], $servicesStepShorts[$serviceStepShort]);

                $elementStep = null;
                $serviceStep = null;
                if (!empty($elementStepShort)) {
                    $elementStep = $filteredSteps[$elementStepShort];
                }

                if (!empty($serviceStepShort)) {
                    $serviceStep = $filteredSteps[$serviceStepShort];
                }
                ?>
                <div class="col-sm-12 col-md-6 mt-20">
                    <div class="row">
                        @if(!is_null($serviceStep))

                            <div class="col-md-6">
                                <img class="img-responsive pr-10 d-inline pull-left"
                                     src="{{asset('images/icons/'.$serviceStep->short.'.png')}}">
                                <h4>@lang('cooperation/tool/general-data/interest.index.steps.'.$serviceStep->short.'.title')</h4>
                            </div>
                            <div class="col-md-6">
                                @component('cooperation.tool.components.step-question', ['id' => 'user_interest', 'name' => 'user_interests.'.$serviceStep->id.'.interest_id'])
                                    @component('cooperation.tool.components.input-group', ['inputType' => 'select', 'inputValues' => $interests, 'userInputValues' => $buildingOwner->userInterestsForSpecificType(get_class($serviceStep), $serviceStep->id)->forMe()->get(), 'userInputColumn' => 'interest_id'])
                                        <select id="user_interest" class="form-control"
                                                name="user_interests[{{$serviceStep->id}}][interest_id]">
                                            @foreach($interests as $interest)
                                                <option @if(old('user_interests.interest_id.*', \App\Helpers\Hoomdossier::getMostCredibleValue($buildingOwner->userInterestsForSpecificType(get_class($serviceStep), $serviceStep->id), 'interest_id')) == $interest->id) selected="selected"
                                                        @endif value="{{ $interest->id }}">{{ $interest->name}}</option>
                                            @endforeach
                                        </select>
                                    @endcomponent
                                @endcomponent
                            </div>
                        @else
                            <div class="col-md-offset-6"></div>
                        @endif
                    </div>
                </div>

                <div class="col-sm-12 col-md-6 mt-20">
                    <div class="row">
                        @if(!is_null($elementStep))
                            <div class="col-md-6">
                                <img class="img-responsive pr-10 d-inline pull-left"
                                     src="{{asset('images/icons/'.$elementStep->short.'.png')}}">
                                <h4>@lang('cooperation/tool/general-data/interest.index.steps.'.$elementStep->short.'.title')</h4>
                            </div>
                            <div class="col-md-6">
                                @component('cooperation.tool.components.step-question', ['id' => 'user_interest', 'name' => 'user_interests.'.$elementStep->id.'.interest_id'])
                                    @component('cooperation.tool.components.input-group', ['inputType' => 'select', 'inputValues' => $interests, 'userInputValues' => $buildingOwner->userInterestsForSpecificType(get_class($elementStep), $elementStep->id)->forMe()->get(), 'userInputColumn' => 'interest_id'])
                                        <select id="user_interest" class="form-control"
                                                name="user_interests[{{$elementStep->id}}][interest_id]">
                                            @foreach($interests as $interest)
                                                <option @if(old('user_interests.interest_id.*', \App\Helpers\Hoomdossier::getMostCredibleValue($buildingOwner->userInterestsForSpecificType(get_class($elementStep), $elementStep->id), 'interest_id')) == $interest->id) selected="selected"
                                                        @endif value="{{ $interest->id }}">{{ $interest->name}}</option>
                                            @endforeach
                                        </select>
                                    @endcomponent
                                @endcomponent
                            </div>
                        @else
                            <div class="col-md-offset-6"></div>
                        @endif
                    </div>
                </div>
            @endfor
        </div>

        <div class="row mt-20">
            <div class="col-sm-12">
                <h4>
                    <i data-target="#motivation-info" class="glyphicon glyphicon-info-sign glyphicon-padding collapsed" aria-expanded="false"></i>
                    @lang('cooperation/tool/general-data/interest.index.motivation.title.title')
                    @component('cooperation.tool.components.help-modal', ['id' => 'motivation-info'])
                        @lang('cooperation/tool/general-data/interest.index.motivation.title.help')
                    @endcomponent
                </h4>
            </div>
            <?php
            $oldMotivations = old('motivations');
            $motivationsToSelect = empty(is_array($oldMotivations) ? $oldMotivations : []) ? $userMotivations->pluck('motivation_id')->toArray() : $motivationsToSelect;
            ?>
            <div class="col-sm-12">
                @component('cooperation.tool.components.step-question', ['id' => 'user_motivations.id.*'])
                    <select id="motivation" class="form-control" name="user_motivations[id][]" multiple="multiple">
                        @foreach($motivations as $motivation)
                            <option @if(in_array($motivation->id, $motivationsToSelect)) selected
                                    @endif value="{{$motivation->id}}">{{$motivation->name}}</option>
                        @endforeach
                    </select>
                @endcomponent
            </div>
        </div>
        <div class="row mt-25">
            <div class="col-sm-6">
                <?php
                $renovationPlanAnswerOptions = [
                    1 => __('cooperation/tool/general-data/interest.index.motivation.renovation-plans.options.yes-within-2-year'),
                    2 => __('cooperation/tool/general-data/interest.index.motivation.renovation-plans.options.yes-within-5-year'),
                    0 => __('cooperation/tool/general-data/interest.index.motivation.renovation-plans.options.none')
                ];
                // default value for this should be 0, but null will be returned anyway so we have to do some uitzondering op de regel stuff
                $mostCredibleValueForRenovationPlan = \App\Helpers\Hoomdossier::getMostCredibleValue($buildingOwner->energyHabit(), 'renovation_plans');
                $selectedRenovationPlanValue = is_null($mostCredibleValueForRenovationPlan) ? 0 : $mostCredibleValueForRenovationPlan;
                ?>
                @component('cooperation.tool.components.step-question', ['id' => 'renovation-plans', 'translation' => 'cooperation/tool/general-data/interest.index.motivation.renovation-plans'])
                    @component('cooperation.tool.components.input-group', ['inputType' => 'select', 'inputValues' => $renovationPlanAnswerOptions, 'userInputValues' => $userEnergyHabitsForMe, 'userInputColumn' => 'renovation_plans'])
                        <select name="user_energy_habits[renovation_plans]" class="form-control" id="">
                            @foreach($renovationPlanAnswerOptions as $value => $renovationPlanAnswerOption)
                                <option
                                        @if($selectedRenovationPlanValue  === $value)
                                            selected="selected"
                                        @endif
                                        value="{{$value}}">{{$renovationPlanAnswerOption}}</option>
                            @endforeach
                        </select>
                    @endcomponent
                @endcomponent
            </div>
            <div class="col-sm-6">
                @component('cooperation.tool.components.step-question', ['id' => 'renovation-plans', 'translation' => 'cooperation/tool/general-data/interest.index.motivation.building-complaints'])
                    @component('cooperation.tool.components.input-group', ['inputType' => 'input', 'userInputValues' => $userEnergyHabitsForMe, 'userInputColumn' => 'building_complaints'])
                        <input type="text" name="user_energy_habits[building_complaints]" class="form-control"
                               value="{{\App\Helpers\Hoomdossier::getMostCredibleValue($buildingOwner->energyHabit(), 'building_complaints')}}">
                    @endcomponent
                @endcomponent
            </div>
        </div>

        @include('cooperation.tool.includes.comment', [
            'translation' => 'cooperation/tool/general-data/interest.index.comment'
        ])
    </form>
@endsection

@push('js')
    <!-- select2 -->
    <script src="{{asset('js/select2.js')}}"></script>

    <script>
        $(document).ready(function () {
            $('#motivation').select2();
        });
    </script>
@endpush