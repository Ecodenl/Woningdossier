@component('cooperation.pdf.components.new-page')
    <div id="simple-scan-answers" class="container">
        <h2>
            @lang('pdf/user-report.simple-scan-answers.title')
        </h2>
        <p>
            @lang('pdf/user-report.simple-scan-answers.text')
        </p>

        @foreach($dump as $stepShort => $results)
            <div class="group">
                <h4>
                    {{ \App\Models\Step::findByShort($stepShort)->name }}
                </h4>
                <div class="row">
                    @foreach($results as $key => $answer)
                        <div class="col-6">
                            <p>
                                {{ $headers["{$stepShort}.{$key}"] }}
                            </p>
                        </div>
                        <div class="col-6">
                            <p>
                                {!! $answer !!}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
@endcomponent