<div class="step-intro">
    {{--            <img src="{{public_path('images/icons/'.$stepSlug.'.png')}}" alt="">--}}
    <img src="{{asset('images/icons/'.$stepSlug.'.png')}}" alt="">
    <h2>{{\App\Models\Step::whereSlug($stepSlug)->first()->name}}</h2>
    <p>@lang('pdf/user-report.step-description.'.$stepSlug)</p>
</div>
