<textarea wire:model.lazy="filledInAnswers.{{$toolQuestion['short']}}" id="{{$toolQuestion->short}}" class="form-input"
          placeholder="{{$toolQuestion->placeholder}}"
          @if(($disabled ?? false))
              disabled
          @else
              x-on:input-updated.window="$el.setAttribute('disabled', true);"
              x-on:input-update-processed.window="$el.removeAttribute('disabled');"
          @endif
></textarea>
