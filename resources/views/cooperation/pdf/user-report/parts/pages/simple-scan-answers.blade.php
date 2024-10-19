@component('cooperation.pdf.user-report.components.new-page', ['id' => 'simple-scan-answers'])
    <h1 class="my-2">
        {{ strip_tags(__('pdf/user-report.pages.simple-scan-answers.title')) }}
    </h1>
    <p>
        @lang('pdf/user-report.pages.simple-scan-answers.text')
    </p>

    @foreach($simpleDump as $stepShort => $results)
        <div class="group">
            <h3>
                {{ \App\Models\Step::findByShort($stepShort)->name }}
            </h3>

            @include('cooperation.pdf.user-report.parts.step-summary')
        </div>
    @endforeach

    @if($alerts->isNotEmpty())
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