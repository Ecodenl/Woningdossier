@component('cooperation.pdf.user-report.components.new-page', ['id' => 'front-page', 'withPageBreak' => false])
    @php
        $logo = $userCooperation->firstMedia(MediaHelper::LOGO);

        $backgroundUrl = ($buildingBackground = $building->firstMedia(\App\Helpers\MediaHelper::BUILDING_IMAGE)) instanceof \App\Models\Media
            ? route('cooperation.media.serve', ['cooperation' => $userCooperation, 'media' => $buildingBackground])
            : (
                ($pdfBackground = $userCooperation->firstMedia(MediaHelper::PDF_BACKGROUND)) instanceof \App\Models\Media
                ? route('cooperation.media.serve', ['cooperation' => $userCooperation, 'media' => $pdfBackground])
                : asset('images/background.jpg')
            );
        \Log::debug($backgroundUrl)
    @endphp
    <div class="w-100">
        <div class="float-left" style="width: 50%">
            <h1 class="p-0 m-0">
                {{$user->getFullName()}}
            </h1>
            @php
                // A H1 tag by default has a font size of 26px (per pdf.css).
                // Max chars before line break: 19;
                $address = "{$building->street} {$building->number} {$building->extension}";

                $postalCity = "{$building->postal_code} {$building->city}";
                $longestLine = max(strlen($address), strlen($postalCity));

                $h1FontSize = 26;
                if ($longestLine > 84) {
                    $h1FontSize = 8; // Basically too small to read but what gives, your address shouldn't be _this_ long :)
                } elseif ($longestLine > 40) {
                    $h1FontSize = 14;
                } elseif ($longestLine > 22) {
                    $h1FontSize = 16;
                } elseif ($longestLine > 19) {
                    $h1FontSize = 20;
                }
            @endphp
            <h1 style="font-size: {{"{$h1FontSize}px"}};">
                {{$address}}
                <br>
                {{$postalCity}}
            </h1>

            <div id="cooperation-info">
                <h2 class="text-green-600">
                    {{ $userCooperation->name }}
                </h2>
                <h2 class="text-green-600">
                    {{ date('d-m-Y') }}
                    <br>
                    @php
                        $coachNames = implode(', ', $connectedCoachNames);

                        // A H2 tag by default has a font size of 20px (per pdf.css).
                        // The first row supports about 10 chars and the lines after that 30 (based on 20px font size).
                        // When the size reduces, the amount of chars that fit changes. There's also unknown cut-off
                        // points. So, we will basically attempt a best effort to ensure a maximum height.
                        $length = strlen($coachNames);
                        $h2FontSize = 20;
                        if ($length > 200) {
                            $h2FontSize = 8;
                        } elseif ($length > 90) {
                            $h2FontSize = 10;
                        } elseif ($length > 40) {
                            $h2FontSize = 14;
                        }
                    @endphp
                    @if(! empty($coachNames))
                        <span style="font-size: {{"{$h2FontSize}px"}};">
                            {{ strip_tags(trans_choice('pdf/user-report.pages.front-page.connected-coaches', count($connectedCoachNames))) . ' ' . $coachNames }}
                        </span>
                    @endif
                </h2>
            </div>
        </div>
        <div class="float-right">
            @if($logo instanceof \App\Models\Media)
                <img class="float-right" src="{{ route('cooperation.media.serve', ['cooperation' => $userCooperation, 'media' => $logo]) }}" alt="{{ $userCooperation->name }}"
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
        {{ strip_tags(__('pdf/user-report.pages.front-page.title')) }}
    </h1>
@endcomponent