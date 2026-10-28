@component('cooperation.pdf.user-report.components.new-page', ['id' => 'simple-scan-answers'])
    <h1 class="my-2">
        {{ strip_tags(__('pdf/user-report.pages.simple-scan-answers.title')) }}
    </h1>
    <p>
        @lang('pdf/user-report.pages.simple-scan-answers.text')
    </p>

    {{-- Once the woonplan is SmartTwin's, the steps SmartTwin takes over read as "Gegevens woning
         bekijken" does; the rest as before (decision 2026-10-15-smarttwin-rapport-na-omslagpunt, P5). --}}
    @if($smartTwin)
        @include('cooperation.pdf.user-report.parts.smart-twin.house-data')
    @endif

    @foreach($simpleDump as $stepShort => $results)
        @php $summarizedStep = \App\Models\Step::findByShort($stepShort); @endphp
        @continue($smartTwin && ($summarizedStep->isProvidedBySmartTwin() || $summarizedStep->mirrorsStepProvidedBySmartTwin()))
        <div class="group">
            <h3>
                {{ $summarizedStep->name }}
            </h3>

            @include('cooperation.pdf.user-report.parts.step-summary')
        </div>
    @endforeach

    {{-- They can rest on answers from before SmartTwin (P7). --}}
    @if(! $smartTwin && $alerts->isNotEmpty())
        @include('cooperation.pdf.user-report.parts.page-break')

        <div class="group">
            <h4>
                {{ strip_tags(__('pdf/user-report.alerts.title')) }}
            </h4>
            @foreach($alerts as $alert)
                <p class="{{ \App\Services\Models\AlertService::TYPE_MAP[$alert->type] }}">
                    {{$alert->text}}
                </p>
            @endforeach
        </div>
    @endif
@endcomponent