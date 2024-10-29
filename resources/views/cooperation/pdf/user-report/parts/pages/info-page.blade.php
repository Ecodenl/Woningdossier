@component('cooperation.pdf.user-report.components.new-page', ['id' => 'info-page'])
    <div class="group">
        <h2>
            {{ strip_tags(__('pdf/user-report.pages.info-page.subsidy.title')) }}
        </h2>
        @if(empty($subsidyRegulations))
            <p>
                @lang('pdf/user-report.pages.info-page.subsidy.none-available')
            </p>
        @else
            <p>
                @lang('pdf/user-report.pages.info-page.subsidy.available-for')
            </p>
            @foreach($subsidyRegulations as $regulation)
                <div class="group">
                    <div class="row">
                        <h5>
                            {{ $regulation['Title'] }}
                        </h5>
                    </div>
                    <div class="row">
                        <p>
                            {{ implode(', ', $regulation['advisable_names']) }}
                        </p>
                    </div>
                </div>
            @endforeach
        @endif
        <p class="pt-2">
            @lang('pdf/user-report.pages.info-page.subsidy.text')
        </p>
    </div>

    <div class="group">
        <h2>
            @lang('pdf/user-report.pages.info-page.calculations-are-indicative.title')
        </h2>
        <p>
            @lang('pdf/user-report.pages.info-page.calculations-are-indicative.text')
        </p>
    </div>
    <div class="group">
        <h2>
            @lang('pdf/user-report.pages.info-page.more-info.title')
        </h2>
        <p>
            @lang('pdf/user-report.pages.info-page.more-info.text', ['cooperation' => $userCooperation->name])
            @if(! empty($userCooperation->website_url))
                @lang('pdf/user-report.pages.info-page.more-info.website', ['url' => "<a href='{$userCooperation->website_url}' rel='nofollow' target='_blank'>{$userCooperation->website_url}</a>"])
            @endif
        </p>
    </div>
@endcomponent