<div class="flex flex-row flex-wrap w-full">
    <div class="w-full">
        @php
            $considerableName = $considerable->measure_name ?? $considerable->name;
            // TODO: Properly translate this
            $translation = "Wilt u {$considerableName} laten doorrekenen?";
        @endphp
        @component('cooperation.tool.components.step-question', [
                    'id' => 'considerable',
                    'translation' => $translation,
                    'class' => 'considerable'
                ])

            @slot('sourceSlot')
                @include('cooperation.tool.components.source-list', [
                    'inputType' => 'radio',
                    'inputValues' => \App\Helpers\ConsiderableHelper::getConsiderableValues(),
                    'userInputValues' => \App\Models\Considerable::forMe($buildingOwner)->get(),
                    'userInputColumn' => 'is_considering'
                ])
            @endslot
            @php
                $considerablePivot = $buildingOwner
                    ->considerablesForModel($considerable)
                    ->wherePivot('input_source_id', \App\Models\InputSource::findByShort('master')->id)
                    ->first();
            @endphp
            @foreach(\App\Helpers\ConsiderableHelper::getConsiderableValues() as $boolean => $considerableText)
                @php($uuid = \App\Helpers\Str::uuid())
                <div class="radio-wrapper @if($loop->iteration % 2 === 0) pl-3 @else pr-3 @endif">
                    <input type="radio" id="{{$uuid}}"
                           name="considerables[{{$considerable->id}}][is_considering]" value="{{$boolean}}"
                           @if(
                            old("considerables.{$considerable->id}.is_considering",
                            $considerablePivot instanceof Illuminate\Database\Eloquent\Model ?
                             $considerablePivot->pivot->is_considering : 1) == $boolean)
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
