<div class="w-full grid grid-rows-1 grid-cols-4 grid-flow-row gap-4">
    <?php
    // so, this is everything but ideal; but this logic came after the complete tool question structure was designed
    // tldr; quick fix, should be improved later on.
    $questionValues = $toolQuestion->getQuestionValues();
    switch ($toolQuestion->short) {
        case('building-type'):
            $conditionalQuestion = \App\Models\ToolQuestion::findByShort('building-type-category');

            $conditionalQuestionValues = $conditionalQuestion->getQuestionValues();


            $buildingTypeCategoryId = $building->getAnswer(
                \App\Models\InputSource::findByShort(App\Models\InputSource::MASTER_SHORT),
                \App\Models\ToolQuestion::findByShort('building-type-category')
            );

            // only one option would mean there are no multiple building types for the category, thus the page is redundant.
            // so multiple building types = next step.
            $matchedBuildingType = App\Models\BuildingType::where('building_type_category_id', $buildingTypeCategoryId)->get();
            $questionValues = $questionValues->whereIn('value', $matchedBuildingType->pluck('id')->toArray());

            break;
    }
    ?>
    @foreach($questionValues as $toolQuestionValue)
        @php
            $id = $toolQuestionValue['short'] ?? $toolQuestionValue['calculate_value'] ?? $toolQuestionValue['value'];
        @endphp
        <div class="radio-wrapper media-wrapper">
            <input type="radio"
                   id="{{$id}}"
                   wire:model="filledInAnswers.{{$toolQuestion['id']}}"
                   value="{{$toolQuestionValue['value']}}"
            >
            <label for="{{$id}}">
                            <span class="media-icon-wrapper">
                                <i class="{{$toolQuestionValue['extra']['icon'] ?? ''}}"></i>
                            </span>
                <span class="checkmark"></span>
                <span>{{$toolQuestionValue['name']}}</span>
            </label>
        </div>
    @endforeach
</div>