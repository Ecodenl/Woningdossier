@component('cooperation.pdf.user-report.components.new-page', ['id' => 'action-plan'])
    <h2>
        @lang('pdf/user-report.action-plan.title')
    </h2>
    <p>
        @lang('pdf/user-report.action-plan.text')
    </p>

    <div class="group">
        @php $stepShort = $scanShort === \App\Models\Scan::LITE ? 'usage-lite-scan' : 'usage-quick-scan'; @endphp
        <h4>
            @lang('pdf/user-report.action-plan.usage.current')
        </h4>

        <div class="row">
            <div class="col-2">
                <p>
                    {{ Str::ucfirst(__('general.unit.gas.title')) }}
                </p>
            </div>
            <div class="col-9">
                <p>
                    {!! $simpleDump[$stepShort]['question_amount-gas'] !!}
                </p>
            </div>
        </div>
        <div class="row">
            <div class="col-2">
                <p>
                    {{ Str::ucfirst(__('general.unit.electric.title')) }}
                </p>
            </div>
            <div class="col-10">
                <p>
                    {!! $simpleDump[$stepShort]['question_amount-electricity'] !!}
                </p>
            </div>
        </div>
    </div>

    <div class="group">
        <h4>
            @lang('pdf/user-report.action-plan.usage.kengetallen')
        </h4>

        <div class="row">
            <div class="col-2">
                <p>
                    {{ Str::ucfirst(__('general.unit.gas.title')) }}
                </p>
            </div>
            <div class="col-10">
                <p>
                    {!! number_format(Kengetallen::EURO_SAVINGS_GAS, 2, ',', '.') . ' € / m<sup>3</sup>' !!}
                </p>
            </div>
        </div>
        <div class="row">
            <div class="col-2">
                <p>
                    {{ Str::ucfirst(__('general.unit.electric.title')) }}
                </p>
            </div>
            <div class="col-10">
                <p>
                    {!! number_format(Kengetallen::EURO_SAVINGS_ELECTRICITY, 2, ',', '.') . ' € / m<sup>3</sup>' !!}
                </p>
            </div>
        </div>
    </div>

    @foreach($categorizedAdvices as $category => $advices)
        @if($advices->isNotEmpty())
            @php $showDetails = $category !== \App\Services\UserActionPlanAdviceService::CATEGORY_COMPLETE; @endphp
            <div class="group">
                <h3>
                    @lang("pdf/user-report.action-plan.categories.{$category}")
                </h3>

                @if($showDetails)
                    <div class="row">
                        <div class="col-6">
                            <h4>
                                @lang('pdf/user-report.action-plan.advices.measure')
                            </h4>
                        </div>
                        <div class="col-3 text-center">
                            <h4>
                                @lang('pdf/user-report.action-plan.advices.cost-indication')
                            </h4>
                        </div>
                        <div class="col-3 text-center">
                            <h4>
                                @lang('pdf/user-report.action-plan.advices.savings')
                            </h4>
                        </div>
                    </div>
                @endif

                @foreach($advices as $advice)
                    <div class="row">
                        <div class="col-6">
                            <p>
                                {{ $advice->userActionPlanAdvisable->name }}
                            </p>
                        </div>
                        @if($showDetails)
                            {{-- Mapped after retrieval --}}
                            <div class="col-3 text-center">
                                <p>
                                    {{ $advice->costs }}
                                </p>
                            </div>
                            <div class="col-3 text-center">
                                <p>
                                    {{ $advice->savings_money }}
                                </p>
                            </div>
                        @endif
                    </div>
                @endforeach

                @if($showDetails)
                    <div class="row">
                        <div class="col-6">
                            <h4>
                                @lang('pdf/user-report.action-plan.advices.total')
                            </h4>
                        </div>
                        <div class="col-3 text-center">
                            <h4>
                                {{ $categorizedTotals[$category]['costs'] }}
                            </h4>
                        </div>
                        <div class="col-3 text-center">
                            <h4>
                                {{ $categorizedTotals[$category]['savings'] }}
                            </h4>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    @endforeach

    @if($adviceComments->isNotEmpty())
        <div class="group">
            <h4>
                @lang('pdf/user-report.action-plan.comment')
            </h4>
            @foreach($adviceComments as $comment)
                @include('cooperation.pdf.user-report.parts.comment', [
                    'label' => $comment->inputSource->name,
                    'comment' => $comment->comment,
                ])
            @endforeach
        </div>
    @endif
@endcomponent