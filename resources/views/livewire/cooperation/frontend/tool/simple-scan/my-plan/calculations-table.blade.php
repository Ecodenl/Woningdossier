<div class="flex w-1/4 justify-center" x-data="modal()">
    <button class="btn btn-outline-purple" x-on:click="toggle()">
        @lang('cooperation/frontend/tool.my-plan.calculations.title')
    </button>

    @component('cooperation.frontend.layouts.components.modal', [
        'class' => 'data-table',
        'style' => 'width: auto; max-width: 75%;',
        'header' => __('cooperation/frontend/tool.my-plan.calculations.title'),
    ])
        <div class="mb-4">
            {!! __('cooperation/frontend/tool.my-plan.calculations.description') !!}
        </div>

        <table class="table fancy-table">
            <thead>
                <tr>
                    <th>
                        @lang('cooperation/frontend/tool.my-plan.calculations.table.info')
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
                @foreach($tableData as $data)
                    <tr>
                        <td>
                            {{ $data['name'] }}
                        </td>
                        <td class="flex items-center">
                            {!! $data['value'] !!}
                        </td>
                        <td>
                            {{ $data['source'] }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endcomponent
</div>