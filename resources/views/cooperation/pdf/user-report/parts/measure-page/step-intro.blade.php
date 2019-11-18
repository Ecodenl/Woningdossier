<div class="step-intro">
    {{--            <img src="{{public_path('images/icons/'.$stepShort.'.png')}}" alt="">--}}
    <img src="{{asset('images/icons/'.$stepShort.'.png')}}" alt="">
    <h2>{{\App\Models\Step::whereSlug($stepShort)->first()->name}}</h2>
    <p>@lang('pdf/user-report.step-description.'.$stepShort)</p>
</div>
