<div class="flex w-1/4 justify-center" x-data="modal()">
    <button class="btn btn-outline-purple" x-on:click="toggle()">
        @lang('cooperation/frontend/tool.my-plan.calculations.title')
    </button>

    @component('cooperation.frontend.layouts.components.modal', [
        'style' => 'width: auto; max-width: 95%;',
        'header' => __('cooperation/frontend/tool.my-plan.calculations.title'),
    ])
        <table class="table fancy-table">
            <thead>
                <tr>
                    <th>
                        @lang('cooperation/frontend/tool.my-plan.calculations.table.question')
                    </th>
                    <th>
                        @lang('cooperation/frontend/tool.my-plan.calculations.table.value')
                    </th>
                    <th>
                        @lang('cooperation/frontend/tool.my-plan.calculations.table.source')
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach($toolQuestions as $toolQuestion)
                    @if(array_key_exists($toolQuestion->short, $answers))
                        <tr>
                            <td>
                                {{ $toolQuestion->name }}
                            </td>
                            <td class="flex items-center">
                                {!! $answers[$toolQuestion->short]['answer'] !!}
                            </td>
                            <td>
                                {{ $answers[$toolQuestion->short]['input_source_name'] }}
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    @endcomponent
</div>