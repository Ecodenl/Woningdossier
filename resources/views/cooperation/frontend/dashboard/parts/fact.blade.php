{{--
    One row of the "this is your house" panel: an icon, what it is, and what it says.

    Takes either a 'value' (plain text) or an 'energyLabel' (an EnergyLabel model, rendered as the
    coloured arrow). Both may be missing -- the row still renders, so the panel does not change
    shape depending on how much the sources happened to know. That matters most for the estimated
    energy label, which has no source at all yet.
--}}
@php
    $value = $value ?? null;
    $energyLabel = $energyLabel ?? null;
@endphp

<div class="building-fact">
    <i class="icon-md {{ $icon }}"></i>

    <div>
        <dt>{{ $label }}</dt>
        <dd>
            @if($energyLabel instanceof \App\Models\EnergyLabel)
                <i class="icon-sm {{ $energyLabel->iconClass() }}"></i>
            @elseif(! empty($value))
                {{ $value }}
            @else
                <span class="building-fact-unknown">@lang('home.dashboard.building.unknown')</span>
            @endif
        </dd>
    </div>
</div>
