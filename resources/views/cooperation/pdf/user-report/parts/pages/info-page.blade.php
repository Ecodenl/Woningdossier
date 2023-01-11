@component('cooperation.pdf.components.new-page')
    <div id="info-page" class="container">
        <div class="group">
            <h4>
                @lang('pdf/user-report.info-page.calculations-are-indicative.title')
            </h4>
            <p>
                @lang('pdf/user-report.info-page.calculations-are-indicative.text')
            </p>
        </div>
        <div class="group">
            <h4>
                @lang('pdf/user-report.info-page.more-info.title')
            </h4>
            <p>
                @lang('pdf/user-report.info-page.more-info.text', ['cooperation' => $cooperation->name])
                @if(! empty($cooperation->website_url))
                    @lang('pdf/user-report.info-page.more-info.website', ['url' => "<a href='{$cooperation->website_url}' rel='nofollow' target='_blank'>{$cooperation->website_url}</a>"])
                @endif
            </p>
        </div>
    </div>
@endcomponent