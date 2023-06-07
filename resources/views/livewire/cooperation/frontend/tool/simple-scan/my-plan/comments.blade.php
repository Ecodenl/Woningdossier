<div>
    <div class="w-full flex flex-wrap bg-blue-100 pb-8 px-3 lg:px-8">
        @php
            $disableResident = \App\Helpers\HoomdossierSession::isUserObserving() || $currentInputSource->short !== $residentInputSource->short;
            $disableCoach = \App\Helpers\HoomdossierSession::isUserObserving() || $currentInputSource->short !== $coachInputSource->short;

            $residentQuestion = new \App\Models\ToolQuestion([
                'name' => __("cooperation/frontend/tool.my-plan.comments.{$residentInputSource->short}"),
                'placeholder' => __('default.form.input.comment-placeholder'),
                'short' => 'residentComment',
            ]);

            $coachQuestion = new \App\Models\ToolQuestion([
                'name' => __("cooperation/frontend/tool.my-plan.comments.{$coachInputSource->short}"),
                'placeholder' => __('default.form.input.comment-placeholder'),
                'short' => 'coachComment',
            ]);
        @endphp
        @component('cooperation.frontend.layouts.components.form-group', [
            'label' => __("cooperation/frontend/tool.my-plan.comments.{$residentInputSource->short}"),
            'class' => 'w-full md:w-1/2 md:pr-3',
            'withInputSource' => false,
            'id' => 'comments-resident',
            'inputName' => 'residentCommentText'
        ])
            @slot('header')
                @lang('cooperation/frontend/tool.my-plan.comments.resident')
            @endslot

            @include("cooperation.tool-question-type-templates.wysiwyg-textarea-popup.show", [
                'disabled' => $disableResident,
                'toolQuestion' => $residentQuestion,
            ])
        @endcomponent
        @component('cooperation.frontend.layouts.components.form-group', [
            'label' => __("cooperation/frontend/tool.my-plan.comments.{$coachInputSource->short}"),
            'class' => 'w-full md:w-1/2 md:pl-3',
            'withInputSource' => false,
            'id' => 'comments-coach',
            'inputName' => 'coachCommentText'
        ])
            @slot('header')
                @lang('cooperation/frontend/tool.my-plan.comments.coach')
            @endslot

            @include("cooperation.tool-question-type-templates.wysiwyg-textarea-popup.show", [
                'disabled' => $disableCoach,
                'toolQuestion' => $coachQuestion,
            ])
        @endcomponent
    </div>
</div>
