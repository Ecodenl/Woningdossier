@extends('cooperation.tool.layout')

@section('content')

    <div class="container">

        <div class="row">
            <div class="col-md-12">
                <div class="text-center">
                    @include('cooperation.tool.includes.top-alerts')
                    @include('cooperation.tool.parts.progress')
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                {{--
                   We check if the current step is the building detail
                --}}
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#main-tab" data-toggle="tab">{{$currentStep->name}}</a>
                    </li>
                    @if($currentStep->hasSubSteps())
                        @foreach($cooperation->getSubStepsForStep($currentStep) as $subStep)
                            <li @if($subStep->short == $currentSubStep->short)class="active"@endif>
                                <a href="{{route("cooperation.tool.{$currentStep->short}.{$subStep->short}.index")}}" >{{$subStep->name}}</a>
                            </li>
                        @endforeach
                    @endif

                    @if(isset($currentStep) && $currentStep->hasQuestionnaires())
                        @foreach($currentStep->questionnaires as $questionnaire)
                            @if($questionnaire->isActive())
                                <li>
                                    <a href="#questionnaire-{{$questionnaire->id}}" data-toggle="tab">{{$questionnaire->name}}</a>
                                </li>
                            @endif
                        @endforeach
                    @endif
                </ul>

                <div class="tab-content">
                    @include('cooperation.layouts.custom-questionnaire')

                    {{--    @include('cooperation.layouts.custom-questionnaire')--}}

                    <div class="panel tab-pane active tab-pane panel-default" id="main-tab">
                        <div class="panel-heading">
                            <h3>
                                Dit is een biertje
                            </h3>

                            @if(!in_array(Route::currentRouteName(), ['cooperation.tool.index', 'cooperation.tool.my-plan.index']) && !\App\helpers\HoomdossierSession::isUserObserving())
                                <button id="submit-main-form" class="pull-right btn btn-primary">
                                    @if(in_array(Route::currentRouteName(), ['cooperation.tool.ventilation-information.index', 'cooperation.tool.heat-pump.index']))
                                        @lang('default.buttons.next-page')
                                    @else
                                        @lang('default.buttons.next')
                                    @endif
                                </button>
                            @elseif(in_array(Route::currentRouteName(), ['cooperation.tool.my-plan.index']) && $buildingHasCompletedGeneralData && \App\Helpers\Hoomdossier::user()->hasRoleAndIsCurrentRole(['coach', 'resident']) && !\App\Helpers\HoomdossierSession::isUserObserving())
                                <form action="{{route('cooperation.file-storage.store', ['fileType' => $pdfReportFileType->short])}}" method="post">
                                    {{csrf_field()}}
                                    <button style="margin-top: -35px"
                                            type="submit"
                                            class="pull-right btn btn-primary pdf-report">
                                        {{ \App\Helpers\Translation::translate('my-plan.download.title') }}
                                    </button>
                                </form>
                            @endif
                            <div class="clearfix"></div>
                        </div>

                        <div class="panel-body">
                            body
                            @yield('step_content', '')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('js')

@endpush