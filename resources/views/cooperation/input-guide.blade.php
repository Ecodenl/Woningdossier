<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="stylesheet" href="{{asset('css/frontend/app.css')}}">
    <title>Hoomdossier input guide</title>
</head>
<body>
<div class="w-full">
    {{-- Nav bar --}}
    <div class="grid grid-flow-row grid-cols-3 w-full bg-white">
        <div>
            <i class="icon-hoomdossierm"></i>
        </div>
        <div>
            {{-- Whit space --}}
        </div>
        <div class="flex flex-row justify-end space-x-4">
            <span class="text-blue-100">
                Start
            </span>
            <p>
                <a>
                    Basisadvies
                </a>
            </p>
            {{-- I assume this will be chat-alert only if there are actual messages --}}
            <i class="icon-md icon-chat-alert"></i>
            <div>
                <i class="icon-md icon-account-circle"></i>
                <i class="icon-sm icon-arrow-down"></i>
            </div>
        </div>
    </div>
    {{-- Step progress --}}
    <div class="w-full space-x-4">
        <div class="border-b-2 border-solid border-purple">
            <i class="icon-sm icon-check-circle-purple"></i>
            <span class="text-purple">Woninggegevens</span>
        </div>
    </div>
</div>
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 pt-8 flex flex-wrap space-y-20">
    <div class="w-full">

    </div>

    {{-- White space --}}
    <div class="flex w-full h-10"></div>
</div>
</body>
</html>