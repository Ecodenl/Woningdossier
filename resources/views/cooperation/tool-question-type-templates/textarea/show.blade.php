<textarea wire:model="filledInAnswers.{{$toolQuestion['id']}}" id="{{$toolQuestion->short}}" class="form-input"
          placeholder="{{$toolQuestion->placeholder}}" @if(($disabled ?? false)) disabled @endif
></textarea>