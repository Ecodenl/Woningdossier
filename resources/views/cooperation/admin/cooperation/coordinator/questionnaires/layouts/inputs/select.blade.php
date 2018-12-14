<label for="">Vraag</label>
@foreach(config('woningdossier.supported_locales') as $locale)
    <div class="form-group">
        <div class="input-group">
            <span class="input-group-addon">{{$locale}}</span>
            <input name="questions[{{$question->id}}][question][{{$locale}}]" placeholder="Vraag" type="text" value="{{$question->getTranslation('name', $locale)->translation}}" class="form-control">
        </div>
    </div>
@endforeach

<?php $questionOptionCount = 0; ?>
@foreach($question->questionOptions as $questionOption)
    <?php $questionOptionCount++ ?>
    <label for="">Optie {{$questionOptionCount}}</label>
    <div class="option-group">
        @foreach(config('woningdossier.supported_locales') as $locale)
            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-addon">{{$locale}}</span>
                    <input name="questions[{{$question->id}}][options][{{$questionOption->id}}][{{$locale}}]" placeholder="Vraag" type="text" value="{{$questionOption->getTranslation('name', $locale)->translation}}" class="form-control">
                </div>
            </div>
        @endforeach
    </div>
@endforeach

{{-- For every existing question, we want to add a new option group field --}}

{{-- quick maths--}}
<label for="">Optie {{$questionOptionCount + 1}} (toevoegen)</label>
<?php $uuid = \Ramsey\Uuid\Uuid::uuid4(); ?>
@foreach(config('woningdossier.supported_locales') as $locale)
    <div class="form-group">
        <div class="input-group">
            <span class="input-group-addon">{{$locale}}</span>
            <input name="questions[{{$question->id}}][options][{{$uuid}}][{{$locale}}]" placeholder="Vraag" type="text" class="form-control option-text">
        </div>
    </div>
@endforeach
