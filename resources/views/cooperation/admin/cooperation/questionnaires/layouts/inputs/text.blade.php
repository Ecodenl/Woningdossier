<label for="">@lang('cooperation/admin/cooperation/questionnaires.shared.types.default-label')</label>
@foreach(config('hoomdossier.supported_locales') as $locale)
    <?php $translation = $question->getTranslation('name', $locale) instanceof \App\Models\Translation ? $question->getTranslation('name', $locale)->translation : ''; ?>
    <div class="form-group">
        <div class="input-group">
            <span class="input-group-addon">{{$locale}}</span>
            <input name="questions[{{$question->id}}][question][{{$locale}}]"
                   placeholder="@lang('cooperation/admin/cooperation/questionnaires.shared.types.default-placeholder')"
                   type="text" value="{{ old("questions.{$question->id}.question.{$locale}", $translation) }}"
                   class="form-control">
        </div>
    </div>
@endforeach
