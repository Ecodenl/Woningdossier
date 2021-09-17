<div class="flex flex-row flex-wrap w-full">
    <div class="w-full">
        @component('cooperation.tool.components.step-question', [
                    'id' => 'considerable', 'translation' => "Wilt u deze maatregel laten doorrekenen?",
                ])

            @slot('sourceSlot')
                @include('cooperation.tool.components.source-list', [
                    'inputType' => 'radio',
                    'inputValues' => \App\Helpers\ConsiderableHelper::getConsiderableValues(),
                    'userInputValues' => $buildingOwner->considerables($considerable)->get(), 'userInputColumn' => 'is_considering'
                ])
            @endslot
            @php
                $considerable = $buildingOwner
                    ->considerablesForModel($considerable)
                    ->wherePivot('input_source_id', \App\Models\InputSource::findByShort('master')->id)
                    ->first();
            @endphp
            @foreach(\App\Helpers\ConsiderableHelper::getConsiderableValues() as $boolean => $considerableText)
                @php($uuid = \App\Helpers\Str::uuid())
                <div class="radio-wrapper pr-3">
                    <input type="radio" id="{{$uuid}}"
                           name="considerables[is_considering]" value="{{$boolean}}"
                           @if($considerable instanceof Illuminate\Database\Eloquent\Model
                            && $considerable->pivot->is_considering == $boolean)
                           checked
                            @endif
                    >
                    <label for="{{$uuid}}">
                        <span class="checkmark"></span>
                        <span>{{$considerableText}}</span>
                    </label>
                </div>


            @endforeach
        @endcomponent
    </div>
</div>
