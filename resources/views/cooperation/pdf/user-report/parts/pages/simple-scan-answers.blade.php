@component('cooperation.pdf.user-report.components.new-page', ['id' => 'simple-scan-answers'])
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
            @foreach($results as $key => $answer)
                @if(Str::contains($key, 'comment'))
                    {{-- Comments get extra spacing --}}
                    <div class="pt-3">
                        @include('cooperation.pdf.user-report.parts.comment', [
                            'label' => $headers["{$stepShort}.{$key}"],
                            'comment' => $answer,
                        ])
                    </div>
                @else
                    {{-- JSON answers --}}
                    @if(is_array($answer))
                        <div class="row">
                            <div class="col-6">
                                <p class="pr-1">
                                    {{ $headers["{$stepShort}.{$key}"] }}
                                </p>
                            </div>
                        </div>
                        @foreach($answer as $name => $subAnswer)
                            @include('cooperation.pdf.user-report.parts.answer', [
                                'label' => $name,
                                'answer' => $subAnswer,
                                'class' => 'pl-3',
                            ])
                        @endforeach
                    @else
                        @include('cooperation.pdf.user-report.parts.answer', [
                            'label' => $headers["{$stepShort}.{$key}"],
                            'answer' => $answer,
                        ])
                    @endif
                @endif
            @endforeach
        </div>
    @endforeach
@endcomponent