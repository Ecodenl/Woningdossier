@component('cooperation.pdf.user-report.components.new-page', ['id' => 'front-page'])
    <h1>
        @lang('pdf/user-report.front-page.title')
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
        $logo = $cooperation->firstMedia(MediaHelper::LOGO);
    @endphp
    <div id="cooperation-logo">
        @if($logo instanceof \App\Models\Media)
            <img src="{{ pdfAsset($logo->getPath()) }}" alt="{{ $cooperation->name }}" width="250">
        @else
            <h3>
                {{ $cooperation->name }}
            </h3>
        @endif
    </div>

    <div id="cooperation-info">
        <h2>
            {{ $cooperation->name }}
        </h2>
        <p>
            {{ __('pdf/user-report.front-page.date') . ' ' . date('d-m-Y') }}
            <br>
            @php $coachNames = implode(', ', $connectedCoachNames); @endphp
            @if(! empty($coachNames))
                {{ trans_choice('pdf/user-report.front-page.connected-coaches', count($connectedCoachNames)) . ' ' . $coachNames }}
            @endif
        </p>
    </div>
@endcomponent