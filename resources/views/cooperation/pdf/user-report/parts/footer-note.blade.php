<htmlpagefooter name="page-footer">
    <div class="float-right">
        <p class="text-right">
            <small>
                {{ $userCooperation->name . ' - ' . strip_tags(__('pdf/user-report.defaults.page')) }} {PAGENO}
                <br>
                {{ date('d-m-Y') }}
            </small>
        </p>
    </div>
</htmlpagefooter>