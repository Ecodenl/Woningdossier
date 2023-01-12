<htmlpagefooter name="page-footer">
    <div class="pull-right">
        <p class="text-right">
            <small>
                {{ $cooperation->name . ' - ' . __('pdf/user-report.defaults.page') }} {PAGENO}
                <br>
                {{ date('d-m-Y') }}
            </small>
        </p>
    </div>
</htmlpagefooter>