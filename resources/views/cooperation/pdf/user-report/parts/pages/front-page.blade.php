@component('cooperation.pdf.user-report.components.new-page', ['id' => 'front-page', 'withPageBreak' => false])
    <h1>
        @lang('pdf/user-report.pages.front-page.title')
    </h1>

    <div id="user-info">
        <h3>
            {{$user->getFullName()}}
        </h3>
        <p>
            {{$building->street}} {{$building->number}} {{$building->extension}}
            <br>
            {{$building->postal_code}} {{$building->city}}
        </p>
    </div>

    @php
        $logo = $userCooperation->firstMedia(MediaHelper::LOGO);
    @endphp
    <div id="cooperation-logo">
        @if($logo instanceof \App\Models\Media)
            <img src="{{ pdfAsset($logo->getPath()) }}" alt="{{ $userCooperation->name }}" width="250">
        @else
            <h3>
                {{ $userCooperation->name }}
            </h3>
        @endif
    </div>

    <div id="cooperation-info">
        <h2>
            {{ $userCooperation->name }}
        </h2>
        <p>
            {{ __('pdf/user-report.pages.front-page.date') . ' ' . date('d-m-Y') }}
            <br>
            @php $coachNames = implode(', ', $connectedCoachNames); @endphp
            @if(! empty($coachNames))
                {{ trans_choice('pdf/user-report.pages.front-page.connected-coaches', count($connectedCoachNames)) . ' ' . $coachNames }}
            @endif
        </p>
    </div>
@endcomponent