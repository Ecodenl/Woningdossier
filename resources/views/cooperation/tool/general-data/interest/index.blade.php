@extends('cooperation.tool.layout')

@push('css')
    <link rel="stylesheet" href="{{asset('css/select2/select2.min.css')}}">
@endpush

@section('step_content')

    <form class="form-horizontal" method="POST" id="main-form" action="{{ route('cooperation.tool.general-data.usage.store') }}" autocomplete="off">
        {{ csrf_field() }}
        <div class="row">
            <?php /** @var \Illuminate\Support\Collection $steps */ ?>
            @foreach($steps->forget(0) as $step)
                <div class="col-sm-12 col-md-6 mt-20">
                    <div class="row">
                        <div class="col-md-6">
                            <img class="img-responsive pr-10 d-inline pull-left" src="{{asset('images/icons/'.$step->short.'.png')}}">
                            <h4>@lang('general-data/interest.steps.'.$step->short.'.title')</h4>
                        </div>
                        <div class="col-md-6">
                            @component('cooperation.tool.components.step-question', ['id' => 'user_interest'])
                                @include('cooperation.tool.parts.user-interest-select', ['interestedInType' => get_class($step), 'interestedInId' => $step->id])
                            @endcomponent
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row mt-20">
            <div class="col-sm-12">
                <h4>@lang('general-data/interest.motivation.title.title')</h4>
                <p>@lang('general-data/interest.motivation.title.help')</p>
            </div>
            <?php
                $oldMotivations = old('motivations');
                $motivationsToSelect = empty(is_array($oldMotivations) ? $oldMotivations : []) ? $userMotivations->pluck('motivation_id')->toArray() : $motivationsToSelect;
            ?>
            <div class="col-sm-12">
                <select id="motivation" class="form-control" name="motivations[]" multiple="multiple">
                    @foreach($motivations as $motivation)
                        <option @if(in_array($motivation->id, $motivationsToSelect)) selected @endif value="{{$motivation->id}}">{{$motivation->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row mt-25">
            <div class="col-sm-6">
                <?php
                    $renovationPlanAnswerOptions = [
                        1 => __('general-data/interest.motivation.renovation-plans.options.yes-within-2-year'),
                        2 =>  __('general-data/interest.motivation.renovation-plans.options.yes-within-5-year'),
                        0 =>  __('general-data/interest.motivation.renovation-plans.options.none')
                    ]
                ?>
                @component('cooperation.tool.components.step-question', ['id' => 'renovation-plans', 'translation' => 'general-data/interest.motivation.renovation-plans'])
                    @component('cooperation.tool.components.input-group', ['inputType' => 'select', 'inputValues' => $renovationPlanAnswerOptions, 'userInputValues' => $userEnergyHabitsForMe, 'userInputColumn' => 'element_value_id'])
                        <select name="user_energy_habits[renovation_plans]" class="form-control" id="">
                            @foreach($renovationPlanAnswerOptions as $value => $renovationPlanAnswerOption)
                                <option value="{{$value}}">{{$renovationPlanAnswerOption}}</option>
                            @endforeach
                        </select>
                    @endcomponent
                @endcomponent
            </div>
            <div class="col-sm-6">
                @component('cooperation.tool.components.step-question', ['id' => 'renovation-plans', 'translation' => 'general-data/interest.motivation.building-complaints'])
                    @component('cooperation.tool.components.input-group', ['inputType' => 'input', 'userInputValues' => $userEnergyHabitsForMe, 'userInputColumn' => 'element_value_id'])
                        <input type="text" name="user_energy_habits[building_complaints]" class="form-control" value="{{\App\Helpers\Hoomdossier::getMostCredibleValue($buildingOwner->energyHabit(), 'building_complaints')}}">
                    @endcomponent
                @endcomponent
            </div>
        </div>
        @include('cooperation.tool.includes.comment', [
            'translation' => 'general-data/interest.comment'
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