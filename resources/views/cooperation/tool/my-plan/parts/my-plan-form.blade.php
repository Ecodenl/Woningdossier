<form  action="{{ route('cooperation.tool.my-plan.store', ['cooperation' => $cooperation]) }}" method="post">
    {{ csrf_field() }}
    @foreach($advices as $measureType => $stepAdvices)
        <div class="row">

            <div class="col-md-12">
                <h2>@if($measureType == 'energy_saving') {{ \App\Helpers\Translation::translate('my-plan.energy-saving-measures.title') }} @else {{ \App\Helpers\Translation::translate('my-plan.maintenance-measures.title') }} @endif</h2>
            </div>


            <div class="col-md-12">
                <table class="table table-condensed table-responsive">
                    <thead>
                    <tr>
                        <th style="width: 8%">{{ \App\Helpers\Translation::translate('my-plan.columns.more-info.title') }}</th>
                        <th style="width: 5%">{{ \App\Helpers\Translation::translate('my-plan.columns.interest.title') }}</th>
                        <th style="width: 45%">{{ \App\Helpers\Translation::translate('my-plan.columns.measure.title') }}</th>
                        <th style="width: 12%">{{ \App\Helpers\Translation::translate('my-plan.columns.costs.title') }}</th>
                        <th style="width: 12%">{{ \App\Helpers\Translation::translate('my-plan.columns.savings-costs.title') }}</th>
                        <th style="width: 9%">{{ \App\Helpers\Translation::translate('my-plan.columns.advice-year.title') }}</th>
                        <th style="width: 9%">{{ \App\Helpers\Translation::translate('my-plan.columns.planned-year.title') }}</th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($stepAdvices as $stepSlug => $advicesForStep)
                        @foreach($advicesForStep as $advice)
                            <?php $step = $advice->step ?>
                            <tr>
                                <input type="hidden" name="advice[{{ $advice->id }}][{{$stepSlug}}][measure_type]" value="{{$measureType}}">
                                <input type="hidden" class="measure_short" value="{{$advice->userActionPlanAdvisable->short}}">
                                <td>
                                    <a type="#" data-toggle="collapse" data-target="#more-info-{{$advice->id}}"> <i class="glyphicon glyphicon-chevron-down"></i> </a>
                                </td>

                                <td>
                                    <input @if(\App\Helpers\HoomdossierSession::isUserObserving()) disabled="disabled" @endif class="interested-checker" name="advice[{{ $advice->id }}][{{$stepSlug}}][interested]" value="1" type="checkbox" id="advice-{{$advice->id}}-planned" @if($advice->planned) checked @endif />
                                </td>
                                <td>
                                    {{ $advice->userActionPlanAdvisable->measure_name }}
                                    <a href="#warning-modal" role="button" class="measure-warning" data-toggle="modal" style="display:none;"><i class="glyphicon glyphicon-warning-sign" role="button" data-toggle="modal" title="" style="color: #ffc107"></i></a>
                                </td>
                                <td>
                                    {{ $advice->getCost(false, true) }}
                                </td>
                                <td>
                                    {{ Hoomdossier::getUnitForColumn('costs') }} {{ \App\Helpers\NumberFormatter::format($advice->savings_money, 0, true) }}
                                </td>
                                <td class="advice-year">
                                    {{ $advice->year }}
                                </td>
                                <td>
                                    <input value="{{ is_null($advice->planned_year) && $advice->planned ? $advice->year : $advice->planned_year }}"  @if(\App\Helpers\HoomdossierSession::isUserObserving()) disabled="disabled" @endif type="text" maxlength="4" size="4" class="form-control planned-year" name="advice[{{ $advice->id }}][{{ $stepSlug }}][planned_year]" />
                                </td>
                            </tr>
                            <tr class="collapse" id="more-info-{{$advice->id}}" >
                                <td colspan="2"></td>
                                <td colspan="">
                                    <strong>{{ \App\Helpers\Translation::translate('my-plan.columns.savings-gas.title') }}:</strong>
                                    <br>
                                    <strong>{{ \App\Helpers\Translation::translate('my-plan.columns.savings-electricity.title') }}:</strong>
                                </td>
                                <td>
                                    {{ \App\Helpers\NumberFormatter::format($advice->savings_gas, 0, true) }} m<sup>3</sup>
                                    <br>
                                    {{ \App\Helpers\NumberFormatter::format($advice->savings_electricity, 0, true) }} kWh
                                </td>
                                <td colspan="3">
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                    </tbody>
                </table>
                @if(!\App\Helpers\HoomdossierSession::isUserObserving())
                    <a href="{{route('cooperation.conversation-requests.index',  ['cooperation' => $cooperation, 'requestType' => \App\Services\PrivateMessageService::REQUEST_TYPE_COACH_CONVERSATION])}}" class="btn btn-primary">@lang('woningdossier.cooperation.tool.my-plan.conversation-requests.request')</a>
                @endif
            </div>

        </div>
    @endforeach
</form>