<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="stylesheet" href="{{ mix('css/frontend/app.css') }}">
    <title>Hoomdossier styleguide</title>
</head>
<body id="app-body">
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 pt-8 flex flex-wrap space-y-20">
    <!-- Typography -->
    <div class="w-full">
        <div class="w-full border-b border-solid border-black border-opacity-25 py-2 mb-4">
            <span class="pr-3 bg-white text-sm font-normal text-blue-500 uppercase">
                Logo
            </span>
        </div>
        <div class="flex w-full">
            <div class="flex items-center w-full space-x-12">
                <i class="icon-xl icon-hoom-logo"></i>
                <i class="icon-hoomdossier w-36 h-36"></i>
            </div>
        </div>
    </div>
    <!-- Colors -->
    <div class="w-full">
        <div class="w-full border-b border-solid border-black border-opacity-25 py-2 mb-4">
            <span class="pr-3 bg-white text-sm font-normal text-blue-500 uppercase">
                Colors
            </span>
        </div>
        <div class="flex w-full">
            <div class="w-3/12">
                <span class="block pr-3 mb-2 bg-white text-sm text-blue-500 font-light">
                    Primary
                </span>

                <div class="space-y-4">
                    <div class="grid grid-cols-3 grid-rows-1 grid-flow-row gap-7">
                        <div class="bg-green text-center p-4 text-sm text-white font-light rounded-lg h-24 w-24 flex flex-col justify-between">
                            <span>.bg-green</span>
                            <span>#2CA982</span>
                        </div>
                        <div class="bg-purple text-center p-4 text-sm text-white font-light rounded-lg h-24 w-24 flex flex-col justify-between">
                            <span>.bg-purple</span>
                            <span>#622181</span>
                        </div>
                        <div class="bg-white text-center p-4 text-sm text-blue-500 font-light rounded-lg h-24 w-24 flex flex-col justify-between border-2 border-solid border-blue-100">
                            <span>.bg-white</span>
                            <span>#FFFFFF</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 grid-rows-1 grid-flow-row gap-7">
                        <div class="bg-blue text-center p-4 text-sm text-white font-light rounded-lg h-24 w-24 flex flex-col justify-between">
                            <span>.bg-blue</span>
                            <span>#414C57</span>
                        </div>
                        <div class="bg-blue-500 text-center p-4 text-sm text-white font-light rounded-lg h-24 w-24 flex flex-col justify-between">
                            <span>.bg-blue-500</span>
                            <span>#647585</span>
                        </div>
                        <div class="bg-blue-100 text-center p-4 text-sm text-blue-500 font-light rounded-lg h-24 w-24 flex flex-col justify-between">
                            <span>.bg-blue-100</span>
                            <span>#F0F1F2</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-1/12"></div>

            <div class="w-5/12">
                <span class="block pr-3 mb-2 bg-white text-sm text-blue-500 font-light">
                    Secondary
                </span>

                <div class="grid grid-cols-5 grid-rows-1 grid-flow-row gap-7">
                    <div class="bg-orange text-center p-4 text-sm text-white font-light rounded-lg h-24 w-24 flex flex-col justify-between">
                        <span>.bg-orange</span>
                        <span>#FF7F4A</span>
                    </div>
                    <div class="bg-yellow text-center p-4 text-sm text-white font-light rounded-lg h-24 w-24 flex flex-col justify-between">
                        <span>.bg-yellow</span>
                        <span>#FFBD5A</span>
                    </div>
                    <div class="bg-red text-center p-4 text-sm text-white font-light rounded-lg h-24 w-24 flex flex-col justify-between">
                        <span>.bg-red</span>
                        <span>#E31440</span>
                    </div>
                    <div class="bg-blue-800 text-center p-4 text-sm text-white font-light rounded-lg h-24 w-24 flex flex-col justify-between">
                        <span>.bg-blue-800</span>
                        <span>#3781F0</span>
                    </div>
                    <div class="bg-blue-900 text-center p-4 text-sm text-white font-light rounded-lg h-24 w-24 flex flex-col justify-between">
                        <span>.bg-blue-900</span>
                        <span>#1122C8</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Typography -->
    <div class="w-full">
        <div class="w-full border-b border-solid border-black border-opacity-25 py-2 mb-4">
            <span class="pr-3 bg-white text-sm font-normal text-blue-500 uppercase">
                Typography
            </span>
        </div>
        <div class="flex w-full">
            <div class="w-3/12 space-y-8">
                <div class="space-y-3">
                    <h1 class="heading-1 block">
                        Heading 1
                    </h1>
                    <p class="font-bold">.heading-1</p>
                    <div class="flex items-center space-x-3">
                        <div class="w-5 h-5 bg-blue rounded-full"></div>
                        <p>System font Bold 48/54 pt</p>
                    </div>
                    <p class="font-bold">P, Pre-styled</p>
                </div>
                <div class="space-y-3">
                    <h1 class="heading-2 block">
                        Heading 2
                    </h1>
                    <p class="font-bold">.heading-2</p>
                    <div class="flex items-center space-x-3">
                        <div class="w-5 h-5 bg-blue rounded-full"></div>
                        <p>System font Bold 36/42 pt</p>
                    </div>
                    <p class="font-bold no-underline">P, Pre-styled</p>
                </div>
                <div class="space-y-3">
                    <h1 class="heading-3 block">
                        Heading 3
                    </h1>
                    <p class="font-bold">.heading-3</p>
                    <div class="flex items-center space-x-3">
                        <div class="w-5 h-5 bg-blue rounded-full"></div>
                        <p>System font Bold 32/36 pt</p>
                    </div>
                    <p class="font-bold">P, Pre-styled</p>
                </div>
                <div class="space-y-3">
                    <h1 class="heading-4 block">
                        Heading 4
                    </h1>
                    <p class="font-bold">.heading-4</p>
                    <div class="flex items-center space-x-3">
                        <div class="w-5 h-5 bg-blue rounded-full"></div>
                        <p>System font Bold 24/28 pt</p>
                    </div>
                    <p class="font-bold">P, Pre-styled</p>
                </div>
                <div class="space-y-3">
                    <h1 class="heading-5 block">
                        Heading 5
                    </h1>
                    <p class="font-bold">.heading-5</p>
                    <div class="flex items-center space-x-3">
                        <div class="w-5 h-5 bg-blue rounded-full"></div>
                        <p>System font Bold 18/24 pt</p>
                    </div>
                    <p class="font-bold">P, Pre-styled</p>
                </div>
                <div class="space-y-3">
                    <h1 class="heading-6 block">
                        Heading 6
                    </h1>
                    <p class="font-bold">.heading-6</p>
                    <div class="flex items-center space-x-3">
                        <div class="w-5 h-5 bg-blue rounded-full"></div>
                        <p>System font Bold 14/24 pt</p>
                    </div>
                     <p class="font-bold">P, Pre-styled</p>
                </div>
            </div>

            <div class="w-1/12"></div>

            <div class="w-6/12 space-y-8 mt-3">
                <div class="w-11/12 space-y-3">
                    <p>
                        <span class="font-semibold">Body text</span> <span class="font-bold">(P.font-semibold)</span>
                        <br>
                        Hoom deelt kennis en ervaring over woningverduurzaming, we leiden de beste energiecoaches van
                        Nederland op en ontwikkelen digitale tools om het werk van energiecoaches makkelijker te maken.
                        We helpen gemeenten en bewonersinitiatieven met het opzetten en bemensen van een eigen
                        coöperatief energieloket, en bieden ondersteuning in de uitvoering. <span class="font-bold">(P, Pre-styled)</span>
                    </p>

                    <div class="flex items-center space-x-3">
                        <div class="w-5 h-5 bg-blue-500 rounded-full"></div>
                        <p>System font Regular 14/24 pt</p>
                    </div>
                </div>

                <div class="space-y-3">
                    <p class="italic">
                        Caption for extra information or explanation. <span class="font-bold not-italic">(P.italic)</span>
                    </p>

                    <div class="flex items-center space-x-3">
                        <div class="w-5 h-5 bg-blue-500 rounded-full"></div>
                        <p>System font Italic 14/24 pt</p>
                    </div>
                </div>

                <div class="space-x-10">
                    <div class="space-y-3 inline-block">
                        <p>
                            <a>This is a text link <span class="font-bold text-blue-500">(P A, pre-styled)</span></a>
                        </p>

                        <div class="flex items-center space-x-3">
                            <div class="w-5 h-5 bg-purple rounded-full"></div>
                            <p>System font Medium 14/24 pt</p>
                        </div>
                    </div>

                    <div class="space-y-3 inline-block">
                        <a>
                            This is a hyperlink <span class="font-bold no-underline text-blue-500">(A, pre-styled)</span>
                        </a>

                        <div class="flex items-center space-x-3">
                            <div class="w-5 h-5 bg-blue-900 rounded-full"></div>
                            <p>System font Medium 14/24 pt</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Buttons -->
    <div class="w-full">
        <div class="w-full border-b border-solid border-black border-opacity-25 py-2 mb-4">
            <span class="pr-3 bg-white text-sm font-normal text-blue-500 uppercase">
                Buttons
            </span>
        </div>
        <div class="flex w-full">
            <div class="w-3/12 space-y-8">
                <div class="space-x-3">
                    <button class="btn btn-purple inline-block">
                        Sample text
                    </button>
                    <p class="font-bold inline-block">.btn.btn-purple</p>
                </div>
                <div class="space-x-3">
                    <button class="btn btn-orange inline-block">
                        Sample text
                    </button>
                    <p class="font-bold inline-block">.btn.btn-orange</p>
                </div>
            </div>

            <div class="w-2/12"></div>

            <div class="w-4/12 space-y-8">
                <div class="space-x-3">
                    <button class="btn btn-outline-purple inline-block">
                        Sample text
                    </button>
                    <p class="font-bold inline-block">.btn.btn-outline-purple</p>
                </div>
                <div class="space-x-3">
                    <button class="btn btn-outline-orange inline-block">
                        Sample text
                    </button>
                    <p class="font-bold inline-block">.btn.btn-outline-orange</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Icons -->
    <div class="w-full">
        <div class="w-full border-b border-solid border-black border-opacity-25 py-2 mb-4">
            <span class="pr-3 bg-white text-sm font-normal text-blue-500 uppercase">
                Icons
            </span>
        </div>
        <div class="flex flex-wrap w-full space-y-10">
            <p class="font-bold inline-block">
                .icon-{$name} (e.g.: .icon-arrow-left) <br>
                use .icon-xs-xxxl for default sizing, or just use .w-n/.h-n <br>
                Examples: <br>
                .icon-xs.icon-arrow-left <br>
                .w-2.h-2.icon-arrow-right <br>
            </p>
            <div class="flex items-center w-full space-x-7">
                <i class="icon-xs icon-arrow-left"></i>
                <i class="icon-xs icon-arrow-right"></i>
                <i class="icon-sm icon-mail"></i>
                <i class="icon-sm icon-show"></i>
                <i class="icon-sm icon-hide"></i>
                <i class="icon-sm icon-house"></i>
                <i class="icon-sm icon-check-circle"></i>
                <i class="icon-sm icon-close-circle"></i>
                <i class="icon-sm icon-moneybag"></i>
                <i class="icon-sm icon-piggybank"></i>
                <i class="icon-sm icon-savings"></i>
                <i class="icon-sm icon-arrow-left-right"></i>
                <i class="w-3 h-3 icon-arrow-left-bold"></i>
                <i class="w-3 h-3 icon-arrow-right-bold"></i>
                <i class="icon-md icon-arrow-left-circle"></i>
                <i class="icon-md icon-arrow-right-circle"></i>
                <i class="icon-md icon-info"></i>
                <i class="icon-md icon-chat"></i>
                <i class="icon-md icon-account-circle"></i>
            </div>
            <div class="flex items-center w-full space-x-10">
                <i class="icon-lg icon-label-g"></i>
                <i class="icon-lg icon-label-f"></i>
                <i class="icon-lg icon-label-e"></i>
                <i class="icon-lg icon-label-d"></i>
                <i class="icon-lg icon-label-c"></i>
                <i class="icon-lg icon-label-b"></i>
                <i class="icon-lg icon-label-a"></i>
                <i class="icon-lg icon-label-unknown"></i>
            </div>
            <div class="flex items-center w-full space-x-10">
                <i class="icon-xl icon-floor-insulation-none"></i>
                <div></div>
                <i class="icon-xl icon-floor-insulation-moderate"></i>
                <div></div>
                <i class="icon-xl icon-floor-insulation-good"></i>
                <div></div>
                <i class="icon-xl icon-floor-insulation-excellent"></i>
                <i class="icon-xl icon-wall-insulation-none"></i>
                <i class="icon-xl icon-wall-insulation-moderate"></i>
                <i class="icon-xl icon-wall-insulation-good"></i>
                <i class="icon-xl icon-wall-insulation-excellent"></i>
            </div>
            <div class="flex items-center w-full space-x-14">
                <i class="icon-xl icon-gas"></i>
                <i class="icon-xl icon-electric"></i>
                <i class="icon-xl icon-induction"></i>
                <i class="icon-xxxl icon-roof-insulation-none"></i>
                <i class="icon-xxxl icon-roof-insulation-moderate"></i>
                <i class="icon-xxxl icon-roof-insulation-good"></i>
                <i class="icon-xxxl icon-roof-insulation-excellent"></i>
            </div>
            <div class="flex items-center w-full space-x-6">
                <i class="icon-xxl icon-detached-house"></i>
                <i class="icon-xxl icon-two-under-one-roof"></i>
                <i class="icon-xxl icon-end-of-terrace-house"></i>
                <i class="icon-xxl icon-mid-terrace-house"></i>
                <i class="icon-xxl icon-upstairs-apartment-corner"></i>
                <i class="icon-xxl icon-upstairs-apartment-between"></i>
                <i class="icon-xxl icon-apartment-ground-floor-between"></i>
                <i class="icon-xxl icon-apartment-mid-floor-between"></i>
            </div>
            <div class="flex items-center w-full space-x-6">
                <i class="icon-xxl icon-apartment-mid-floor-corner"></i>
                <i class="icon-xxl icon-apartment-upper-floor-between"></i>
                <i class="icon-xxl icon-apartment-upper-floor-corner"></i>
                <i class="icon-xxl icon-apartment-ground-floor-corner"></i>
                <i class="icon-xxl icon-other"></i>
                <i class="icon-xxl icon-pointed-roof"></i>
                <i class="icon-xxl icon-pitched-roof"></i>
                <i class="icon-xxl icon-flat-roof"></i>
            </div>
            <div class="flex items-center w-full space-x-6">
                <i class="icon-xxl icon-kitchen"></i>
                <i class="icon-xxl icon-bathroom"></i>
                <i class="icon-xxl icon-dormer"></i>
                <i class="icon-xxl icon-window-frame"></i>
                <i class="icon-xxl icon-sunroom"></i>
                <i class="icon-xxl icon-attic-room"></i>
                <i class="icon-xxl icon-rounded-roof"></i>
                <i class="icon-xxl icon-flat-pitched-roof"></i>
            </div>
            <div class="flex items-center w-full space-x-6">
                <i class="icon-xl icon-paint-job"></i>
                <i class="icon-xl icon-air-conditioning"></i>
                <i class="icon-xl icon-air-conditioning-hot"></i>
                <i class="icon-xl icon-tools"></i>
                <i class="icon-xl icon-central-heater-gas"></i>
                <i class="icon-xl icon-central-heater-electric"></i>
                <i class="icon-xl icon-central-heater"></i>
                <i class="icon-xl icon-heat-pump"></i>
                <i class="icon-xl icon-infrared-heater"></i>
                <i class="icon-xl icon-district-heating"></i>
            </div>
            <div class="flex items-center w-full space-x-8">
                <i class="icon-xl icon-radiant-floor-heating"></i>
                <i class="icon-xl icon-radiant-wall-heating"></i>
                <i class="icon-xxl icon-radiator"></i>
                <i class="icon-xxl icon-radiator-low-temp"></i>
                <i class="icon-xxl icon-sun-boiler-hot-water"></i>
                <i class="icon-xxl icon-sun-boiler-heating"></i>
                <i class="icon-xxl icon-sun-boiler-both"></i>
                <i class="icon-xxl icon-sun-boiler"></i>
            </div>
            <div class="flex items-center w-full space-x-14">
                <i class="icon-xl icon-solar-panels"></i>
                <i class="icon-xl icon-sustainability"></i>
                <i class="icon-xl icon-plug"></i>
                <i class="icon-xl icon-washing-machine"></i>
                <i class="icon-xl icon-dryer"></i>
                <i class="icon-xl icon-dishwasher"></i>
                <i class="icon-xl icon-co2"></i>
                <i class="icon-xl icon-ventilation"></i>
            </div>
            <div class="flex items-center w-full space-x-10">
                <i class="icon-xl icon-radiator-foil"></i>
                <i class="icon-xl icon-curtains"></i>
                <i class="icon-xl icon-cracks-seams"></i>
                <i class="icon-xl icon-thermostat"></i>
                <i class="icon-xl icon-temperature"></i>
                <i class="icon-xl icon-hydronic-balance-temperature"></i>
                <i class="icon-xl icon-pipes"></i>
                <i class="icon-xl icon-switch"></i>
                <i class="icon-xl icon-building-heat"></i>
            </div>
            <div class="flex items-center w-full space-x-6">
                <i class="icon-xl icon-shower-head"></i>
                <i class="icon-xl icon-timer"></i>
                <i class="icon-xl icon-illumination-shine"></i>
                <i class="icon-xl icon-illumination"></i>
                <i class="icon-xl icon-glass-single"></i>
                <i class="icon-xl icon-glass-double"></i>
                <i class="icon-xl icon-glass-hr"></i>
                <i class="icon-xl icon-glass-hr-p"></i>
                <i class="icon-xl icon-glass-hr-dp"></i>
                <i class="icon-xl icon-glass-hr-tp"></i>
            </div>
            <div class="flex items-center w-full space-x-6">
                <i class="icon-xl icon-persons-one"></i>
                <i class="icon-xxl icon-persons-two"></i>
                <i class="icon-xxl icon-persons-three"></i>
                <i class="icon-xxl icon-persons-four"></i>
                <i class="icon-xxl icon-persons-five"></i>
                <i class="icon-xxl icon-persons-six"></i>
                <i class="icon-xxl icon-persons-seven"></i>
                <i class="icon-xxl icon-persons-more-than-seven"></i>
            </div>
        </div>
    </div>
    {{-- White space --}}
    <div class="flex w-full h-10"></div>
</div>
</body>
</html>