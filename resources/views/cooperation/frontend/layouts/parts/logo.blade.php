@php
    $logo = null;
    if (isset($cooperation) && $cooperation instanceof \App\Models\Cooperation) {
       $logo = $cooperation->firstMedia(MediaHelper::LOGO);
    } else {
       \App\Services\DiscordNotifier::init()->notify("Cooperation is not set (logo)! URL: " . request()->fullUrl() . "; Route: " . optional(request()->route())->getName() . "; Cooperation ID according to session: " . \App\Helpers\HoomdossierSession::getCooperation() . "; Running in console: " . app()->runningInConsole());
    }
@endphp
<div class="flex flex-wrap w-full justify-center items-center">
    <div class="w-36 h-36 flex flex-wrap justify-center items-center">
        @if($logo instanceof \App\Models\Media)
            <img src="{{ $logo->getUrl() }}" alt="{{ $cooperation->name }}">
        @else
            <h4 class="heading-4">
                {{ $cooperation->name }}
            </h4>
        @endif
    </div>
</div>
