<label for="">Vraag</label>
@foreach(config('hoomdossier.supported_locales') as $locale)
    <?php $translation = $question->getTranslation('name', $locale) instanceof \App\Models\Translation ? $question->getTranslation('name', $locale)->translation : ''; ?>
    <div class="form-group">
        <div class="input-group">
            <span class="input-group-addon">{{$locale}}</span>
            <input name="questions[{{$question->id}}][question][{{$locale}}]" placeholder="Vraag" type="text" value="{{old("questions.{$question->id}.question.{$locale}", $translation)}}" class="form-control">
        </div>
    </div>
@endforeach

<?php $questionOptionCount = 0; ?>
@foreach($question->questionOptions as $questionOption)
    <?php $questionOptionCount++; ?>
    <div class="option-group">
        <label for="">Optie {{$questionOptionCount}}</label>
        <input type="hidden" name="questions[{{$question->id}}][option][id]" class="question_option_id" value="{{$questionOption->id}}">
        @foreach(config('hoomdossier.supported_locales') as $locale)
            <?php $translation = $questionOption->getTranslation('name', $locale) instanceof \App\Models\Translation ? $questionOption->getTranslation('name', $locale)->translation : ''; ?>
            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-addon">{{$locale}}</span>
                    <input name="questions[{{$question->id}}][options][{{$questionOption->id}}][{{$locale}}]" placeholder="Vraag" type="text" value="{{ old("questions.{$question->id}.options.{$questionOption->id}.{$locale}", $translation) }}" class="form-control">
                    <span class="input-group-addon">
                          <a href="" class="text-danger">
                              <i class="glyphicon glyphicon-remove"></i>
                          </a>
                    </span>
                </div>
            </div>
        @endforeach
    </div>
@endforeach

{{-- For every existing question, we want to add a new option group field --}}

{{-- quick maths--}}
<?php $uuid = \Ramsey\Uuid\Uuid::uuid4(); ?>
<div class="option-group">
    <label for="">Optie {{$questionOptionCount + 1}}</label>
    @foreach(config('hoomdossier.supported_locales') as $locale)
        <div class="form-group">
            <div class="input-group">
                <span class="input-group-addon">{{$locale}}</span>
                <input name="questions[{{$question->id}}][options][{{$uuid}}][{{$locale}}]" placeholder="Vraag" type="text" class="form-control option-text">
                <span class="input-group-addon">
                    <a href="" class="text-danger">
                      <i class="glyphicon glyphicon-remove"></i>
                    </a>
                </span>
            </div>
        </div>
    @endforeach
</div>
