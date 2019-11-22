<?php
    $shortToUseAsMainSubject = $subStepShort == '-' ? $stepShort : $subStepShort
?>
<div class="step-intro">
    {{--            <img src="{{public_path('images/icons/'.$stepShort.'.png')}}" alt="">--}}
    <img src="{{asset('images/icons/'.$stepShort.'.png')}}" alt="">
    <h2>{{\App\Models\Step::findByShort($shortToUseAsMainSubject)->name}}</h2>
    <p>@lang('pdf/user-report.step-description.'.$shortToUseAsMainSubject )</p>
</div>
