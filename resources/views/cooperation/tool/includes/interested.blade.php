<div class="row">
    <div class="col-sm-12">
        @component('cooperation.tool.components.step-question', ['id' => 'user_interest', 'translation' => 'general.change-interested'])
            @include('cooperation.tool.parts.user-interest-select', compact('interestedInType', 'interestedInId'))
        @endcomponent
    </div>
</div>
