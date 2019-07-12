<div id="general-data">

    <div class="question-answer-section">
        <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.general-data.address-info')}}</p>
        <p>Naam</p><p>Jan text</p>
    </div>

    <div class="question-answer-section">
    <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.general-data.address-info')}}</p>
    <p>data</p>
    </div>

    <div class="question-answer-section">
    <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.general-data.building-info')}}</p>
    <p>data</p>
    </div>

    <div class="question-answer-section">
    <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.general-data.usage-info')}}</p>
    <p>data</p>
    </div>

    <div class="question-answer-section">
        <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.general-data.current-state.title')}}</p>
        <table class="width-100">
            <thead>
                <tr>
                    <th>{{\App\Helpers\Translation::translate('pdf/user-report.general-data.current-state.table.measure')}}</th>
                    <th>{{\App\Helpers\Translation::translate('pdf/user-report.general-data.current-state.table.present-current-situation')}}</th>
                    <th>{{\App\Helpers\Translation::translate('pdf/user-report.general-data.current-state.table.interested-in-improvement')}}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Jill</td>
                    <td>Smith</td>
                    <td>50</td>
                </tr>
                <tr>
                    <td>Eve</td>
                    <td>Jackson</td>
                    <td>94</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="question-answer-section">
    <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.general-data.motivation')}}</p>
    <p>data</p>
    </div>

    <div class="question-answer-section">
        <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.general-data.comment-usage-building')}}</p>
        <p>data</p>
    </div>

    <div class="question-answer-section">
        <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.general-data.resume-energy-saving-measures.title')}}</p>
        <table class="width-100">
            <thead>
                <tr>
                    <th>{{\App\Helpers\Translation::translate('pdf/user-report.general-data.resume-energy-saving-measures.table.planned-year')}}</th>
                    <th>{{\App\Helpers\Translation::translate('pdf/user-report.general-data.resume-energy-saving-measures.table.interested')}}</th>
                    <th>{{\App\Helpers\Translation::translate('pdf/user-report.general-data.resume-energy-saving-measures.table.measure')}}</th>
                    <th>{{\App\Helpers\Translation::translate('pdf/user-report.general-data.resume-energy-saving-measures.table.costs')}}</th>
                    <th>{{\App\Helpers\Translation::translate('pdf/user-report.general-data.resume-energy-saving-measures.table.savings')}}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Jill</td>
                    <td>Smith</td>
                    <td>Smith</td>
                    <td>Smith</td>
                    <td>50</td>
                </tr>
            </tbody>
        </table>
        <p>{{\App\Helpers\Translation::translate('pdf/user-report.general-data.resume-energy-saving-measures.text')}}</p>
    </div>


    <div class="question-answer-section">
        <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.general-data.comment-action-plan')}}</p>
        <p>data</p>
    </div>

    <div class="question-answer-section">
        <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.general-data.calculations-are-indicative.title')}}</p>
        <p>{{\App\Helpers\Translation::translate('pdf/user-report.general-data.calculations-are-indicative.text')}}</p>
    </div>

    <div class="question-answer-section">
        <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.general-data.next-steps.title')}}</p>
        <p>{{\App\Helpers\Translation::translate('pdf/user-report.general-data.next-steps.text', ['cooperation_name' => strtolower($cooperation->name)])}}</p>
    </div>

    <div class="question-answer-section">
        <h2>{{\App\Helpers\Translation::translate('pdf/user-report.general-data.attachment.title')}}</h2>
        <p class="lead">{{\App\Helpers\Translation::translate('pdf/user-report.general-data.attachment.lead')}}</p>
        <p>{!!\App\Helpers\Translation::translate('pdf/user-report.general-data.attachment.text')!!}</p>
    </div>


{{--    <div class="container bg-white" id="user-info">--}}
{{--        <h1>{{$user->getFullName()}}</h1>--}}
{{--        <h1>{{$building->street}} {{$building->number}} {{$building->extension}}</h1>--}}
{{--        <h1>{{$building->postal_code}} {{$building->city}}</h1>--}}
{{--    </div>--}}
{{----}}
{{--    <div id="img-front-page">--}}
{{--        <img src="{{asset('images/pdf-main-images.jpg')}}">--}}
{{--    </div>--}}
{{----}}
{{--    <div class="page-footer bg-white" id="intro">--}}
{{--        <h2 class="text-uppercase">@lang('pdf/user-report.front-page.intro.title')</h2>--}}
{{--        <p>@lang('pdf/user-report.front-page.intro.text')</p>--}}
{{--    </div>--}}

</div>

