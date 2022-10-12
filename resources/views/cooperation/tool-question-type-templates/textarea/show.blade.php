<textarea wire:model.lazy="filledInAnswers.{{$toolQuestion['short']}}" id="{{$toolQuestion->short}}" class="form-input"
          placeholder="{{$toolQuestion->placeholder}}" @if(($disabled ?? false)) disabled @endif
></textarea>
