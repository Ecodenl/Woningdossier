<div class="w-full flex flex-wrap items-center">
    <div class="w-1/3 flex flex-wrap justify-start"></div>
    <div class="w-1/3 flex flex-wrap justify-center">
        <button class="btn btn-outline-purple flex items-center mr-1">
            <i class="icon-xs icon-arrow-left-bold-purple mr-5"></i>
            @lang('cooperation/frontend/shared.defaults.previous')
        </button>
        <button class="btn btn-purple flex items-center ml-1">
            @lang('cooperation/frontend/shared.defaults.next')
            <i class="icon-xs icon-arrow-right-bold-purple ml-5"></i>
        </button>
    </div>
    <div class="w-1/3 flex flex-wrap justify-end">
        <p>
            {!! __('cooperation/frontend/tool.step-count', ['current' => '<span class="font-bold">' . $current .'</span>', 'total' => $total]) !!}
        </p>
    </div>
</div>