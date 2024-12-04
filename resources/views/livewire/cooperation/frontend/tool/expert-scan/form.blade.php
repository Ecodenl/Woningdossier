<div>
    <div class="rounded-md bg-red-50 p-4 mb-4 {{count($failedValidationForSubSteps) == 0 ? 'hidden' : 'flex'}}">
        <div class="flex">
            <div class="shrink-0">
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

    <div x-data="tabs('{{$subSteps->first()->slug}}')"
         x-on:scroll-to-top.window="window.scrollTo({ top: 0, behavior: 'smooth' })"
         wire:ignore.self>

        <div class="hidden sm:block">
            <nav class="nav-tabs" aria-label="Tabs">
                @foreach($subSteps as $subStep)
                    <a x-bind="tab" data-tab="{{ $subStep->slug }}" href="#" wire:ignore>
                        {{$subStep->name}}
                    </a>
                @endforeach
            </nav>
        </div>

        @foreach($subSteps as $subStep)
            <div x-bind="container" data-tab="{{$subStep->slug}}" wire:ignore.self>
                 @include('cooperation.frontend.tool.expert-scan.parts.sub-steppable', [
                    'subSteppables' => $this->subSteppables->where('sub_step_id', $subStep->id)
                 ])
            </div>
        @endforeach
    </div>
</div>

@push('js')
    <script type="module">
        document.addEventListener('change', (event) => {
            let target = event.target;

            let hasWireModel = false;
            for (const attr of target.attributes) {
                if (attr.name.startsWith('wire:model.live')) {
                    // Ensure we don't trigger updates if it's deferred to maintain defer logic.
                    if (! attr.name.includes('defer')) {
                        hasWireModel = true;
                        break;
                    }
                }
            }

            if (hasWireModel) {
                triggerCustomEvent(window, 'input-updated');
                Livewire.dispatch('inputUpdated');
            }
        });

        document.addEventListener('input-update-processed', () => {
            tinymce.remove();
            setTimeout(() => {
                initTinyMCE({
                    content_css: '{{ asset('css/frontend/tinymce.css') }}',
                });
            });
        });
    </script>
@endpush
