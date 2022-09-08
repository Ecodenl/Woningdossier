<div>
    <div class="rounded-md bg-red-50 p-4 mb-4 {{count($failedValidationForSubSteps) == 0 ? 'hidden' : 'flex'}}">
        <div class="flex">
            <div class="flex-shrink-0">
                <!-- Heroicon name: mini/x-circle -->
                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">Er missen wat gegevens bij de volgende onderwerpen</h3>
                <div class="mt-2 text-sm text-red-700">
                    <ul role="list" class="list-disc space-y-1 pl-5">
                        @foreach($failedValidationForSubSteps as $failedValidationForSubStep)
                            <li>{{$failedValidationForSubStep}}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- This example requires Tailwind CSS v2.0+ -->
    <div x-data="{ active: '{{$step->subSteps->first()->slug}}'}"
         x-on:scroll-to-top.window="window.scrollTo({ top: 0, behavior: 'smooth' })"
         wire:ignore.self>

        <div class="hidden sm:block">
            <nav class="flex border-b border-blue border-opacity-50" aria-label="Tabs">
                @foreach($step->subSteps as $subStep)
                    <a x-on:click="active = '{{$subStep->slug}}'" href="#"
                       x-bind:class="{ 'bg-green': active === '{{$subStep->slug}}', 'bg-blue-500': active !== '{{$subStep->slug}}' }"
                       class="no-underline rounded-t-md p-2 text-white" wire:ignore>
                        {{$subStep->name}}
                    </a>
                @endforeach
            </nav>
        </div>

        @foreach($step->subSteps as $subStep)
             <div x-show="active == '{{$subStep->slug}}'" wire:ignore.self>
                @livewire('cooperation.frontend.tool.expert-scan.sub-steppable', ['step' => $step, 'subStep' => $subStep], key($subStep->id))
             </div>
         @endforeach
    </div>
</div>
