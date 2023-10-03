@component('cooperation.pdf.user-report.components.new-page', ['id' => 'front-page', 'withPageBreak' => false])
    @php
        $logo = $userCooperation->firstMedia(MediaHelper::LOGO);
        $background = $userCooperation->firstMedia(MediaHelper::BACKGROUND);
    @endphp
    <div class="w-100">
        <div class="pull-left" style="width: 50%">
            <h1 class="p-0 m-0">
                {{$user->getFullName()}}
            </h1>
            <h1>
                {{$building->street}} {{$building->number}} {{$building->extension}}
                <br>
                {{$building->postal_code}} {{$building->city}}
            </h1>


            <div id="cooperation-info">
                <h2 class="p-0 m-0">
                    {{ $userCooperation->name }}
                </h2>
                <h2>
                    {{ date('d-m-Y') }}
                    <br>
                    @php $coachNames = implode(', ', $connectedCoachNames); @endphp
                    @if(! empty($coachNames))
                        {{ trans_choice('pdf/user-report.pages.front-page.connected-coaches', count($connectedCoachNames)) . ' ' . $coachNames }}
                    @endif
                </h2>
            </div>
        </div>
        <div class="pull-right">
            @if($logo instanceof \App\Models\Media)
                <img class="pull-right" src="{{ pdfAsset($logo->getPath()) }}" alt="{{ $userCooperation->name }}" width="150">
            @else
                <h3>
                    {{ $userCooperation->name }}
                </h3>
            @endif
        </div>
    </div>



    <img src="{{pdfAsset($background->getPath())}}" alt="{{$userCooperation->name}}" style="max-height: 550px; width: 100%">
    <div class="w-100 my-2">
    </div>

    <h1 class="text-center">
        @lang('pdf/user-report.pages.front-page.title')
    </h1>

@endcomponent