<div x-data="{ dropdownOpen: '{{$shouldOpenAlert}}' }" class="relative">
    <button @click="dropdownOpen = !dropdownOpen" class="flex flex-wrap justify-center items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="text-blue-500 h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        <div class="absolute flex-shrink-0 flex -right-1 -top-1">
            <span class="h-3 w-3 rounded-full bg-green" aria-hidden="true"></span>
        </div>
    </button>

    <div x-show="dropdownOpen" @click="dropdownOpen = false" class="fixed inset-0 h-full w-full z-10"></div>

    <div x-show="dropdownOpen" class="border border-blue-500 rounded absolute right-1 mt-2 bg-white rounded-md shadow-lg overflow-hidden z-20 w-96">
        <div class="divide-y divide-blue-500">
            @foreach($alerts as $alert)
                <p class="{{ $typeMap[$alert['type']] }} text-sm p-4">
                    {{$alert->text}}
                </p>
            @endforeach
        </div>
    </div>
</div>