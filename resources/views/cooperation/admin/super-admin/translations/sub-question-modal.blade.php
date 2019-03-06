<div id="{{$id}}" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog"
     aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                @foreach($subQuestion->text as $locale => $text)
                    <div class="form-group">
                        <label for="">@lang('woningdossier.cooperation.admin.super-admin.translations.edit.question', ['locale' => $locale])</label>
                        <input class="form-control question-input"
                               name="language_lines[{{$locale}}][question][{{$subQuestion->id}}]" value="{{$text}}">
                        <label for="">key: {{$subQuestion->group}}.{{$subQuestion->key}}</label>
                    </div>
                @endforeach
                @if($subQuestion->helpText instanceof \Spatie\TranslationLoader\LanguageLine)
                    @foreach($subQuestion->helpText->text as $locale => $text)
                        <div class="form-group">
                            <label for="">@lang('woningdossier.cooperation.admin.super-admin.translations.edit.help', ['locale' => $locale])</label>
                            <textarea class="form-control"
                                      name="language_lines[{{$locale}}][help][{{$subQuestion->helpText->id}}]">{{$text}}</textarea>
                            <label for="">key: {{$subQuestion->helpText->group}}.{{$subQuestion->helpText->key}}</label>
                        </div>
                    @endforeach
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                        data-dismiss="modal">@lang('woningdossier.cooperation.admin.super-admin.translations.edit.close-modal')</button>
            </div>
        </div>
    </div>
</div>