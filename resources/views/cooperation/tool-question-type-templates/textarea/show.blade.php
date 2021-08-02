@component('cooperation.tool-question-type-templates.components.default')
    <div class="input-group">
        <textarea wire:model="filledInAnswers.{{$toolQuestion['id']}}" id="{{$toolQuestion->short}}" class="form-input"
                  placeholder="{{$toolQuestion->placeholder}}"></textarea>
    </div>
@endcomponent