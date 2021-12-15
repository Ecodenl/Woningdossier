<label for="">Vraag</label>
@foreach(config('hoomdossier.supported_locales') as $locale)
    <div class="form-group">
        <div class="input-group">
            <span class="input-group-addon">{{$locale}}</span>
            <input name="questions[{{$question->id}}][question][{{$locale}}]" placeholder="Vraag" type="text"
                   value="{{ old("questions.{$question->id}.question.{$locale}", $question->getTranslation('name', $locale)) }}" class="form-control">
        </div>
    </div>
@endforeach
