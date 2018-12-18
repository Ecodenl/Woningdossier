<label for="">Vraag</label>
@foreach(config('woningdossier.supported_locales') as $locale)
    <?php $translation =  $question->getTranslation('name', $locale) instanceof \App\Models\Translation ? $question->getTranslation('name', $locale)->translation : "" ?>
    <div class="form-group">
        <div class="input-group">
            <span class="input-group-addon">{{$locale}}</span>
            <input name="questions[{{$question->id}}][question][{{$locale}}]" placeholder="Vraag" type="text" value="{{ old("questions.{$question->id}.question.{$locale}", $translation)}}" class="form-control">
        </div>
    </div>
@endforeach
