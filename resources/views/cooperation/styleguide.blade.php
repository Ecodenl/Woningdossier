<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="stylesheet" href="{{asset('css/frontend/app.css')}}">
    <title>Document</title>
</head>
<body>
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 pt-8 flex flex-wrap space-y-10">
    <!-- Typography -->
    <div class="w-full">
        <div class="w-full border-b border-solid border-black border-opacity-25 py-2 mb-4">
            <span class="pr-3 bg-white text-md font-normal text-blue-500 uppercase">
                Logo
            </span>
        </div>
        <div class="flex w-full">
            <div class="w-full space-x-8">
                <img src="{{ asset('images/building-detail.png') }}" alt="PLACEHOLDER" class="w-32 h-32 inline-block">
                <img src="{{ asset('images/building-detail.png') }}" alt="PLACEHOLDER" class="w-32 h-32 inline-block">
            </div>
        </div>
    </div>
    <!-- Colors -->
    <div class="w-full">
        <div class="w-full border-b border-solid border-black border-opacity-25 py-2 mb-4">
            <span class="pr-3 bg-white text-md font-normal text-blue-500 uppercase">
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
                        <div class="bg-green text-center p-4 text-sm text-white rounded-lg h-24 w-24 flex flex-col justify-between">
                            <span>bg-green</span>
                            <span>#2CA982</span>
                        </div>
                        <div class="bg-purple text-center p-4 text-sm text-white rounded-lg h-24 w-24 flex flex-col justify-between">
                            <span>bg-purple</span>
                            <span>#622181</span>
                        </div>
                        <div class="bg-white text-center p-4 text-sm text-black rounded-lg h-24 w-24 flex flex-col justify-between border-2 border-solid border-blue-100">
                            <span>bg-white</span>
                            <span>#FFFFFF</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 grid-rows-1 grid-flow-row gap-7">
                        <div class="bg-blue text-center p-4 text-sm text-white rounded-lg h-24 w-24 flex flex-col justify-between">
                            <span>bg-blue</span>
                            <span>#414C57</span>
                        </div>
                        <div class="bg-blue-500 text-center p-4 text-sm text-white rounded-lg h-24 w-24 flex flex-col justify-between">
                            <span>bg-blue-500</span>
                            <span>#647585</span>
                        </div>
                        <div class="bg-blue-100 text-center p-4 text-sm text-black rounded-lg h-24 w-24 flex flex-col justify-between">
                            <span>bg-blue-100</span>
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
                    <div class="bg-orange text-center p-4 text-sm text-white rounded-lg h-24 w-24 flex flex-col justify-between">
                        <span>bg-orange</span>
                        <span>#FF7F4A</span>
                    </div>
                    <div class="bg-yellow text-center p-4 text-sm text-white rounded-lg h-24 w-24 flex flex-col justify-between">
                        <span>bg-yellow</span>
                        <span>#FFBD5A</span>
                    </div>
                    <div class="bg-red text-center p-4 text-sm text-white rounded-lg h-24 w-24 flex flex-col justify-between">
                        <span>bg-red</span>
                        <span>#E31440</span>
                    </div>
                    <div class="bg-blue-800 text-center p-4 text-sm text-white rounded-lg h-24 w-24 flex flex-col justify-between">
                        <span>bg-blue-800</span>
                        <span>#3781F0</span>
                    </div>
                    <div class="bg-blue-900 text-center p-4 text-sm text-white rounded-lg h-24 w-24 flex flex-col justify-between">
                        <span>bg-blue-900</span>
                        <span>#1122C8</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Typography -->
    <div class="w-full">
        <div class="w-full border-b border-solid border-black border-opacity-25 py-2 mb-4">
            <span class="pr-3 bg-white text-md font-normal text-blue-500 uppercase">
                Typography
            </span>
        </div>
        <div class="flex w-full">
        </div>
    </div>
</div>
</body>
</html>