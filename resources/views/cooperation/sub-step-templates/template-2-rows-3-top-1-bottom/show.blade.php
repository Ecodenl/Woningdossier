<div class="w-full divide-y-2 divide-blue-500 divide-opacity-20 space-y-{{$toolQuestions->count() > 1 ? 10 : 5}} ">

    <?php
    // some necessary crap to display the toolQuestions in the right manor
    $topLeft = $toolQuestions->where('pivot.order', 0)->first();
    $topRightFirst = $toolQuestions->where('pivot.order', 1)->first();
    $topRightSecond = $toolQuestions->where('pivot.order', 2)->first();

    $bottomLeft = $toolQuestions->where('pivot.order', 3)->first();

    ?>
    <div class="w-full flex flex-wrap">
        @component('cooperation.frontend.layouts.components.form-group', [
            'class' => 'form-group-heading w-full lg:w-1/2 lg:pr-3',
            'label' => $topLeft->name,
         ])
            @slot('modalBodySlot')
                <p>
                    {!! $topLeft->help_text !!}
                </p>
            @endslot

            @include("cooperation.tool-question-type-templates.{$topLeft->toolQuestionType->short}.show", ['toolQuestion' => $topLeft])
        @endcomponent

        <div class="w-full lg:w-1/2 lg:pl-3">
            @component('cooperation.frontend.layouts.components.form-group', [
                'class' => 'form-group-heading w-full',
                'label' => $topRightFirst->name,
             ])
                @slot('modalBodySlot')
                    <p>
                        {!! $topRightFirst->help_text !!}
                    </p>
                @endslot

                @include("cooperation.tool-question-type-templates.{$topRightFirst->toolQuestionType->short}.show", ['toolQuestion' => $topRightFirst])
            @endcomponent
            @component('cooperation.frontend.layouts.components.form-group', [
                'class' => 'form-group-heading w-full',
                'label' => $topRightFirst->name,
            ])
                @slot('modalBodySlot')
                    <p>
                        {!! $topRightSecond->help_text !!}
                    </p>
                @endslot

                @include("cooperation.tool-question-type-templates.{$topRightSecond->toolQuestionType->short}.show", ['toolQuestion' => $topRightSecond])
            @endcomponent
        </div>
    </div>
    <div class="w-full pt-5">
        @component('cooperation.frontend.layouts.components.form-group', [
            'class' => 'form-group-heading w-full',
            'label' => $bottomLeft->name,
        ])
            @slot('modalBodySlot')
                <p>
                    {!! $bottomLeft->help_text !!}
                </p>
            @endslot
            <div class="w-1/2">

                @include("cooperation.tool-question-type-templates.{$bottomLeft->toolQuestionType->short}.show", ['toolQuestion' => $bottomLeft])
            </div>
        @endcomponent

    </div>

</div>
