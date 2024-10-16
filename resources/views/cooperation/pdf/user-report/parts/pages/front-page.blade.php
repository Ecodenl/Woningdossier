@component('cooperation.pdf.user-report.components.new-page', ['id' => 'front-page', 'withPageBreak' => false])
    @php
        $logo = $userCooperation->firstMedia(MediaHelper::LOGO);

        $buildingBackground = $building->firstMedia(\App\Helpers\MediaHelper::BUILDING_IMAGE);

        $backgroundUrl = $buildingBackground instanceof \App\Models\Media
            ? pdfAsset($buildingBackground->getPath())
            : (
                ($pdfBackground = $userCooperation->firstMedia(MediaHelper::PDF_BACKGROUND)) instanceof \App\Models\Media
                ? pdfAsset($pdfBackground->getPath())
                : pdfAsset('images/background.jpg')
            );
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
                <h2 class="text-green">
                    {{ $userCooperation->name }}
                </h2>
                <h2 class="text-green">
                    {{ date('d-m-Y') }}
                    <br>
                    @php
                        $coachNames = implode(', ', $connectedCoachNames);

                        // A H2 tag by default has a font size of 20px (per pdf.css).
                        // The first row supports about 10 chars and the lines after that 30 (based on 20px font size).
                        // When the size reduces, the amount of chars that fit changes. There's also unknown cut-off
                        // points. So, we will basically attempt a best effort to ensure a maximum height.
                        $length = strlen($coachNames);
                        $fontSize = 20;
                        if ($length > 200) {
                            $fontSize = 8;
                        } elseif ($length > 90) {
                            $fontSize = 10;
                        } elseif ($length > 40) {
                            $fontSize = 14;
                        }
                    @endphp
                    @if(! empty($coachNames))
                        <span style="font-size: {{"{$fontSize}px"}};">
                            {{ strip_tags(trans_choice('pdf/user-report.pages.front-page.connected-coaches', count($connectedCoachNames))) . ' ' . $coachNames }}
                        </span>
                    @endif
                </h2>
            </div>
        </div>
        <div class="pull-right">
            @if($logo instanceof \App\Models\Media)
                <img class="pull-right" src="{{ pdfAsset($logo->getPath()) }}" alt="{{ $userCooperation->name }}"
                     style="max-height: 250px;">
            @else
                <h3>
                    {{ $userCooperation->name }}
                </h3>
            @endif
        </div>
    </div>

    <div class="text-center mt-10" style="height: 500px;">
        <img src="{{ $backgroundUrl }}" alt="{{$userCooperation->name}}" style="max-height: 500px; width: auto;">
    </div>
    <div class="w-100 my-2">
    </div>

    <h1 class="text-center">
        @lang('pdf/user-report.pages.front-page.title')
    </h1>

@endcomponent