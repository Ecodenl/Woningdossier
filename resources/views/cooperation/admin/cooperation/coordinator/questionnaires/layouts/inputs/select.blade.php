<label for="">Vraag</label>
@foreach(config('woningdossier.supported_locales') as $locale)
    <div class="form-group">
        <div class="input-group">
            <span class="input-group-addon">{{$locale}}</span>
            <input name="questions[edit][{{$question->id}}][question][{{$locale}}]" placeholder="Vraag" type="text" value="{{$question->getTranslation('name', $locale)->translation}}" class="form-control">
        </div>
    </div>
@endforeach

<?php $questionOptionCount = 0; ?>
@foreach($question->QuestionOptions as $QuestionOption)
    <?php $questionOptionCount++ ?>
    <label for="">Optie {{$questionOptionCount}}</label>
    @foreach(config('woningdossier.supported_locales') as $locale)
        <div class="form-group">
            <div class="input-group">
                <span class="input-group-addon">{{$locale}}</span>
                <input name="questions[edit][{{$question->id}}][options][{{$locale}}]" placeholder="Vraag" type="text" value="{{$QuestionOption->getTranslation('name', $locale)->translation}}" class="form-control">
            </div>
        </div>
    @endforeach
@endforeach