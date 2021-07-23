<div>
    {{-- Header row --}}
    <div class="w-full grid grid-rows-1 grid-cols-3 grid-flow-row gap-3 xl:gap-10 mb-3">
        <div class="flex flex-wrap items-center justify-between">
            <div class="flex items-center">
                <div class="w-5 h-5 bg-blue-800 rounded-full mr-3"></div>
                <h5 class="font-semibold text-base text-blue-800">
                    In orde
                </h5>
            </div>
            <i class="icon-md icon-plus-circle cursor-pointer"></i>
        </div>
        <div class="flex flex-wrap items-center justify-between">
            <div class="flex items-center">
                <div class="w-5 h-5 bg-green rounded-full mr-3"></div>
                <h5 class="font-semibold text-base text-green">
                    Nu aanpakken
                </h5>
            </div>
            <i class="icon-md icon-plus-circle cursor-pointer"></i>
        </div>
        <div class="flex flex-wrap items-center justify-between">
            <div class="flex items-center">
                <div class="w-5 h-5 bg-yellow rounded-full mr-3"></div>
                <h5 class="font-semibold text-base text-yellow">
                    Later uitvoeren
                </h5>
            </div>
            <i class="icon-md icon-plus-circle cursor-pointer"></i>
        </div>
    </div>
    <div class="w-full grid grid-rows-1 grid-cols-3 grid-flow-row gap-3 xl:gap-10">
        <div class="card-wrapper">
            <div class="card">
                <div class="icon-wrapper">
                    <i class="icon-ventilation"></i>
                </div>
                <div class="center-info">
                    <h6 class="heading-6">Ventilatie (mechanisch)</h6>
                    <p class="-mt-1">€ 500 - € 700</p>
                    <div class="w-auto h-4 rounded-lg text-xs relative text-green p bg-green bg-opacity-10 flex items-center pl-2">
                        Subsidie mogelijk
                    </div>
                </div>
                <div class="end-info">
                    <div>
                        <i class="icon-md icon-info-light"></i>
                    </div>
                    <p class="font-bold">€ 0</p>
                </div>
            </div>
            @for($i = 0; $i < 4; $i++)
                <div class="card-placeholder"></div>
            @endfor
        </div>
        <div class="card-wrapper">
            @for($i = 0; $i < 5; $i++)
                <div class="card-placeholder"></div>
            @endfor
        </div>
        <div class="card-wrapper">
            @for($i = 0; $i < 5; $i++)
                <div class="card-placeholder"></div>
            @endfor
        </div>
    </div>
</div>
