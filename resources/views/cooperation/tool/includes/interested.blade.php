@isset($typeIds)
    @foreach($typeIds as $elementId)

        <?php
            if($type == "service") {
                $typeName = \App\Models\Service::find($elementId)->name;
            } else {
                $typeName = \App\Models\Element::find($elementId)->name;
            }
        ?>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group add-space">
                    <label for="interest_{{ $type }}_{{ $elementId }}" class="control-label">
                        @uuidlang(general.change-interested.title, ['item' => {{$typeName}}])
                    </label>
                    <select class="form-control" id="interest_{{ $type }}_{{ $elementId }}" name="interest[{{ $type }}][{{ $elementId }}]">
                        @foreach($interests as $interest)
                            <option @if($interest->id == old('user_interest.'.$type.'.'. $elementId . '')) selected @elseif(Auth::user()->getInterestedType($type, $elementId) != null && Auth::user()->getInterestedType($type, $elementId)->interest_id == $interest->id) selected  @endif value="{{ $interest->id }}">{{ $interest->name }}</option>
                        @endforeach
                    </select>

                    @if ($errors->has('interest.'.$elementId))
                        <span class="help-block">
                            <strong>{{ $errors->first('interest.'.$elementId) }}</strong>
                        </span>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
@endisset
