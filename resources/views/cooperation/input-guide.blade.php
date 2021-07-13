<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
<style>
    input[type="text"] +::before {
        content: url('/icons/error-cross.svg');
        display: block;
        /*position: absolute;*/
        /*top: 25%;*/
        /*right: 4%;*/
    }
</style>
    <link rel="stylesheet" href="{{asset('css/frontend/app.css')}}">
    <title>Hoomdossier input guide</title>
</head>
<body>
<div class="w-full">
    {{-- Nav bar --}}
    <div class="grid grid-flow-row grid-cols-3 items-center w-full bg-white shadow-center-lg-purple-50 h-12 px-5 xl:px-20">
        <div>
            <i class="icon-hoomdossier"></i>
        </div>
        <div>
            {{-- White space --}}
        </div>
        <div class="flex flex-row justify-end space-x-4">
            <p>
                <a>
                    Start
                </a>
            </p>
            <p>
                <a class="text-blue">
                    Basisadvies
                </a>
            </p>
            {{-- I assume this will be chat-alert only if there are actual messages --}}
            <i class="icon-md icon-chat-alert"></i>
            <div class="flex items-center">
                <i class="icon-md icon-account-circle mr-1"></i>
                <i class="icon-xs icon-arrow-down"></i>
            </div>
        </div>
    </div>
    {{-- Step progress --}}
    <div class="flex items-center justify-between w-full bg-blue-100 border-b-1 h-16 px-5 xl:px-20">
        <div class=" flex items-center h-full">
            <i class="icon-sm icon-check-circle-dark mr-1"></i>
            <span class="text-blue">Woninggegevens</span>
        </div>
        <div class="step-divider-line"></div>
        <div class="flex items-center h-full">
            <i class="icon-sm bg-purple bg-opacity-25 rounded-full border border-solid border-purple mr-1"></i>
            <span class="text-purple">Gebruik</span>
        </div>
        <div class="step-divider-line"></div>
        <div class="flex items-center h-full">
            <i class="icon-sm bg-transparent rounded-full border border-solid border-blue mr-1"></i>
            <span class="text-blue">Woonwensen</span>
        </div>
        <div class="step-divider-line"></div>
        <div class="flex items-center h-full">
            <i class="icon-sm bg-transparent rounded-full border border-solid border-blue mr-1"></i>
            <span class="text-blue">Woonstatus</span>
        </div>
        <div class="step-divider-line"></div>
        <div class="flex items-center h-full">
            <i class="icon-sm bg-transparent rounded-full border border-solid border-blue mr-1"></i>
            <span class="text-blue">Overige</span>
        </div>
        <div class="border border-blue-500 border-opacity-50 h-1/2"></div>
        <div class="flex items-center justify-start h-full">
            <i class="icon-sm icon-house-dark mr-1"></i>
            <span class="text-blue">Woonplan</span>
        </div>
    </div>
    {{-- Progress bar --}}
    <div class="w-full bg-gray h-2">
        {{-- Define style-width based on step progress divided by total steps --}}
        <div class="h-full bg-purple" style="width: 30%"></div>
    </div>
</div>
<main>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 pt-20 flex flex-wrap space-y-20">
        <div class="w-full">
            <h4 class="heading-4">
                Welke woning past het beste bij uw situatie?
            </h4>
            @component('cooperation.components.form-group', ['label' => 'Voorbeeldwoning opties'])
                <div class="radio-wrapper pr-3">
                    <input type="radio" id="example-building-1" name="example-building" value="1">
                    <label for="example-building-1">
                        <span class="checkmark"></span>
                        <span>Een jaren vijftig woning</span>
                    </label>
                </div>
                <div class="radio-wrapper pl-3">
                    <input type="radio" id="example-building-2" name="example-building" value="2">
                    <label for="example-building-2">
                        <span class="checkmark"></span>
                        <span>Een jaren zestig woning</span>
                    </label>
                </div>
                <div class="radio-wrapper pr-3">
                    <input type="radio" id="example-building-3" name="example-building" value="3">
                    <label for="example-building-3">
                        <span class="checkmark"></span>
                        <span>Een rijtjeshuis</span>
                    </label>
                </div>
                <div class="radio-wrapper pl-3">
                    <input type="radio" id="example-building-4" name="example-building" value="4">
                    <label for="example-building-4">
                        <span class="checkmark"></span>
                        <span>Een nieuwbouw woning</span>
                    </label>
                </div>
            @endcomponent
        </div>

        <div class="w-full">
            <div class="flex items-center justify-between w-full">
                <h4 class="heading-4">
                    Woninggegevens
                </h4>
                <button class="btn btn-purple">
                    Opslaan
                </button>
            </div>

            <div class="flex flex-wrap">
                @component('cooperation.components.form-group', ['label' => 'Onderwerp', 'class' => 'w-full md:w-1/2 md:pr-3'])
                    <input type="text" class="form-input" placeholder="Placeholder">
                @endcomponent
                @component('cooperation.components.form-group', ['label' => 'Onderwerp', 'class' => 'w-full md:w-1/2 md:pl-3'])
                    {{-- WIP dropdown --}}
                @endcomponent
            </div>
            <div class="flex flex-wrap">
                @component('cooperation.components.form-group', ['label' => 'Onderwerp', 'class' => 'w-full md:w-1/2 md:pr-3'])
                    <input type="text" class="form-input" placeholder="Placeholder">
                @endcomponent
                @component('cooperation.components.form-group', ['label' => 'Onderwerp', 'class' => 'w-full md:w-1/2 md:pl-3'])
                    {{-- WIP dropdown --}}
                @endcomponent
            </div>
            <div class="flex flex-wrap">
                @component('cooperation.components.form-group', ['label' => 'Onderwerp', 'class' => 'w-1/2 md:pr-3 form-error required'])
                    <input type="text" class="form-input" placeholder="Error">
                    <p class="form-error-label">
                        Vul de correcte gegevens in
                    </p>
                @endcomponent
            </div>
            <div class="flex flex-wrap">
                @component('cooperation.components.form-group', ['label' => 'Onderwerp', 'class' => 'w-1/2 md:pr-3'])
                    <input type="text" class="form-input" placeholder="Disabled" disabled>
                @endcomponent
            </div>
            <div class="flex flex-wrap">
                @component('cooperation.components.form-group', ['label' => 'Onderwerp', 'class' => 'w-1/2 md:pr-3 required'])
                    <input type="text" class="form-input" placeholder="Verplicht">
                @endcomponent
            </div>

            <div class="flex flex-wrap">
                @component('cooperation.components.form-group', ['label' => 'Onderwerp', 'class' => 'w-full md:w-1/2 md:pr-3'])
                    <textarea class="form-input" placeholder="Text area"></textarea>
                @endcomponent
                @component('cooperation.components.form-group', ['label' => 'Onderwerp', 'class' => 'w-full md:w-1/2 md:pl-3'])
                    <input type="text" class="form-input" placeholder="Placeholder">
                @endcomponent
            </div>
        </div>

        <div class="w-full">
            <div class="flex flex-wrap">
                @component('cooperation.components.form-group', ['label' => 'Onderwerp', 'class' => 'w-full md:w-1/2 md:pr-3'])
                    <input type="text" class="form-input" placeholder="Placeholder">
                @endcomponent
                @component('cooperation.components.form-group', ['label' => 'Onderwerp', 'class' => 'w-full md:w-1/2 md:pl-3'])
                    {{-- WIP media dropdown --}}
                @endcomponent
            </div>
            <div class="flex flex-wrap">
                @component('cooperation.components.form-group', ['label' => 'Onderwerp', 'class' => 'w-1/2 md:pr-3'])
                    <input type="text" class="form-input" placeholder="Placeholder">
                @endcomponent
            </div>
        </div>

        <div class="w-full">
            <div class="flex flex-wrap">
                @component('cooperation.components.form-group', ['label' => 'Onderwerp', 'class' => 'w-full'])
                    <div class="checkbox-wrapper pr-3">
                        <input type="checkbox" id="subject-1" name="subject" value="1">
                        <label for="subject-1">
                            <span class="checkmark"></span>
                            <span>Uitleg gegeven</span>
                        </label>
                    </div>
                    <div class="checkbox-wrapper pr-3">
                        <input type="checkbox" id="subject-2" name="subject" value="2">
                        <label for="subject-2">
                            <span class="checkmark"></span>
                            <span>Interesse in maatregel</span>
                        </label>
                    </div>
                    <div class="checkbox-wrapper pr-3">
                        <input type="checkbox" id="subject-3" name="subject" value="3">
                        <label for="subject-3">
                            <span class="checkmark"></span>
                            <span>Uitgevoerd</span>
                        </label>
                    </div>
                    <div class="checkbox-wrapper pr-3">
                        <input type="checkbox" id="subject-4" name="subject" value="4" disabled>
                        <label for="subject-4">
                            <span class="checkmark"></span>
                            <span>Disabled</span>
                        </label>
                    </div>
                @endcomponent
            </div>
        </div>

        {{-- White space --}}
        <div class="flex w-full h-10"></div>
    </div>
</main>
</body>
</html>