<div class="w-full divide-y-2 divide-blue-500 divide-opacity-20 space-y-{{$toolQuestions->count() > 1 ? 10 : 5}} ">
    @include("cooperation.tool-question-type-templates.{$toolQuestion->toolQuestionType->short}.show")
</div>
