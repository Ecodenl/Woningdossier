<div class="flex flex-row flex-wrap w-full">
    <div class="w-full">
        @component('cooperation.tool.components.step-question', [
            'id' => 'user_interest', 'translation' => "Wilt u deze maatregel laten doorrekenen?",
        ])
            @slot('sourceSlot')
                {{--                @include('cooperation.tool.components.source-list', [--}}
                {{--                    'inputType' => 'select', 'inputValues' => $interests,--}}
                {{--                    'userInputValues' => $buildingOwner->userInterestsForSpecificType($interestedInType, $interestedInId)->forMe()->get(),--}}
                {{--                    'userInputColumn' => 'interest_id',--}}
                {{--                ])--}}
            @endslot
            @component('cooperation.frontend.layouts.components.alpine-select')
                @php
                    $considerable = $buildingOwner
                        ->considerable(get_class($considerable))
                        ->wherePivot('considerable_id', $considerable->id)
                        ->wherePivot('input_source_id', \App\Models\InputSource::findByShort('master')->id)
                        ->first();
                @endphp
                <select id="considerable" class="form-input" name="considerables[is_considering]">
                    @foreach(["Nee", "Ja"] as $boolean => $considerableText)
                        <option value="{{ $boolean }}"
                                @if($considerable instanceof Illuminate\Database\Eloquent\Model
                                    && $considerable->pivot->is_considering == $boolean
                                ) selected="selected" @endif>
                            {{$considerableText}}</option>
                    @endforeach
                </select>
            @endcomponent
        @endcomponent
    </div>
</div>
