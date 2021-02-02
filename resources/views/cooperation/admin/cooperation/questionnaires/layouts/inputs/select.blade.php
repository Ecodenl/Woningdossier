<label for="">@lang('cooperation/admin/cooperation/questionnaires.shared.types.default-label')</label>
@foreach(config('hoomdossier.supported_locales') as $locale)
    <?php $translation = $question->getTranslation('name', $locale) instanceof \App\Models\Translation ? $question->getTranslation('name', $locale)->translation : ''; ?>
    <div class="form-group">
        <div class="input-group">
            <span class="input-group-addon">{{$locale}}</span>
            <input name="questions[{{$question->id}}][question][{{$locale}}]"
                   placeholder="@lang('cooperation/admin/cooperation/questionnaires.shared.types.default-placeholder')"
                   type="text" value="{{old("questions.{$question->id}.question.{$locale}", $translation)}}"
                   class="form-control">
        </div>
    </div>
@endforeach

@foreach($question->questionOptions as $questionOption)
    <div class="option-group">
        <label data-index="{{$loop->iteration}}">@lang('cooperation/admin/cooperation/questionnaires.shared.types.default-option-label') {{$loop->iteration}}</label>
        <input type="hidden" name="questions[{{$question->id}}][option][id]" class="question_option_id" value="{{$questionOption->id}}">
        @foreach(config('hoomdossier.supported_locales') as $locale)
            <?php $translation = $questionOption->getTranslation('name', $locale) instanceof \App\Models\Translation ? $questionOption->getTranslation('name', $locale)->translation : ''; ?>
            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-addon">{{$locale}}</span>
                    <input name="questions[{{$question->id}}][options][{{$questionOption->id}}][{{$locale}}]"
                           placeholder="@lang('cooperation/admin/cooperation/questionnaires.shared.types.default-placeholder')"
                           type="text" value="{{ old("questions.{$question->id}.options.{$questionOption->id}.{$locale}", $translation) }}"
                           class="form-control">
                    <span class="input-group-addon">
                          <a href="" class="text-danger">
                              <i class="glyphicon glyphicon-remove remove-option"></i>
                          </a>
                    </span>
                </div>
            </div>
        @endforeach
    </div>
@endforeach