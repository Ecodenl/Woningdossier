<label for="">Vraag</label>
@foreach(config('woningdossier.supported_locales') as $locale)
    <div class="form-group">
        <div class="input-group">
            <span class="input-group-addon">{{$locale}}</span>
            <input type="hidden" name="questions[edit][{{$question->id}}]type" value="{{$question->type}}">
            <input name="questions[edit][{{$question->id}}][question][{{$locale}}]" placeholder="Vraag" type="text" value="{{$question->translate('name', $locale)}}" class="form-control">
        </div>
    </div>
@endforeach
