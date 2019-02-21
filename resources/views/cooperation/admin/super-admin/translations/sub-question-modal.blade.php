<div id="{{$id}}" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                @foreach($subQuestion->text as $locale => $text)
                    <div class="form-group">
                        <label for="">@lang('woningdossier.cooperation.admin.super-admin.translations.edit.question', ['locale' => $locale])</label>
                        <input class="form-control question-input" name="translation_line[question][{{$subQuestion->id}}]"
                               value="{{$text}}">
                        <label for="">key: {{$subQuestion->group}}.{{$subQuestion->key}}</label>
                    </div>
                @endforeach
                @forelse($subQuestion->helpText->text as $locale => $text)
                    <div class="form-group">
                        <label for="">@lang('woningdossier.cooperation.admin.super-admin.translations.edit.help', ['locale' => $locale])</label>
                        <textarea class="form-control"
                                  name="translation_line[{{$locale}}][help][{{$subQuestion->helpText->id}}]">{{$text}}</textarea>
                        <label for="">key: {{$subQuestion->helpText->group}}.{{$subQuestion->helpText->key}}</label>
                    </div>
                @empty
                @endforelse
            </div>
        </div>
    </div>
</div>