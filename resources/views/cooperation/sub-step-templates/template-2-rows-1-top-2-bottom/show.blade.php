<div class="w-full divide-y-2 divide-blue-500 divide-opacity-20 space-y-{{$toolQuestions->count() > 1 ? 10 : 5}} ">

    <?php
    // some necessary crap to display the toolQuestions in the right manor
    $top = $toolQuestions->where('pivot.order', 0)->first();
    $bottomLeft = $toolQuestions->where('pivot.order', 1)->first();
    $bottomRight = $toolQuestions->where('pivot.order', 2)->first();

    ?>
    <div class="w-full">
        @component('cooperation.frontend.layouts.components.form-group', [
            'class' => 'form-group-heading',
            'label' => $top->name,
        ])
            @slot('modalBodySlot')
                <p>
                    {!! $top->help_text !!}
                </p>
            @endslot

            @include("cooperation.tool-question-type-templates.{$top->toolQuestionType->short}.show", ['toolQuestion' => $top])
        @endcomponent
    </div>
    <div class="pt-5 grid grid-cols-1 gap-x-6 sm:grid-cols-2">
        @component('cooperation.frontend.layouts.components.form-group', [
            'class' => 'form-group-heading w-full',
            'label' => $bottomLeft->name,
        ])
            @slot('modalBodySlot')
                <p>
                    {!! $bottomLeft->help_text !!}
                </p>
            @endslot

            @include("cooperation.tool-question-type-templates.{$bottomLeft->toolQuestionType->short}.show", ['toolQuestion' => $bottomLeft])
        @endcomponent

        @component('cooperation.frontend.layouts.components.form-group', [
            'class' => 'form-group-heading w-full ',
            'label' => $bottomRight->name,
            ])
            @slot('modalBodySlot')
                <p>
                    {!! $bottomRight->help_text !!}
                </p>
            @endslot

            @include("cooperation.tool-question-type-templates.{$bottomRight->toolQuestionType->short}.show", ['toolQuestion' => $bottomRight])
        @endcomponent
    </div>
</div>
