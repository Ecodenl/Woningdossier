@foreach($results as $key => $answer)
    @if(Str::contains($key, 'comment'))
        {{-- Comments get extra spacing --}}
        <div class="py-2">
            @include('cooperation.pdf.user-report.parts.comment', [
                'label' => $headers["{$stepShort}.{$key}"],
                'comment' => $answer,
            ])
        </div>
    @elseif(Str::contains($key, 'label_'))
        {{-- Labels get extra styling --}}
        <div class="row">
            <h5>
                {{ $headers["{$stepShort}.{$key}"] }}
            </h5>
        </div>
    @else
        {{-- JSON answers --}}
        @if(is_array($answer))
            <div class="row">
                <div class="col-8">
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