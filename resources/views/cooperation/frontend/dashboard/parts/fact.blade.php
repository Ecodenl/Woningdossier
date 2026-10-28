{{--
    One row of the "this is your house" panel: an icon, what it is, and what it says.

    Takes either a 'value' (plain text) or an 'energyLabel' (an EnergyLabel model, or a label's name
    such as SmartTwin calculates it, rendered as the coloured arrow). Both may be missing -- the row
    still renders, so the panel does not change shape depending on how much the sources happened to
    know. There are no arrows above A: A+ to A++++ show the one of A with their name next to it.
--}}
@php
    $value = $value ?? null;
    $energyLabel = $energyLabel ?? null;
    $energyLabelName = $energyLabel instanceof \App\Models\EnergyLabel ? $energyLabel->name : $energyLabel;
@endphp

<div class="building-fact">
    <i class="icon-md {{ $icon }}"></i>

    <div>
        <dt>{{ $label }}</dt>
        <dd>
            @if(! empty($energyLabelName))
                <span class="building-fact-label">
                    <i class="icon-sm {{ \App\Models\EnergyLabel::iconClassFor($energyLabelName) }}"></i>
                    @if(\App\Models\EnergyLabel::isAboveA($energyLabelName))
                        {{ $energyLabelName }}
                    @endif
                </span>
            @elseif(! empty($value))
                {{ $value }}
            @else
                <span class="building-fact-unknown">@lang('home.dashboard.building.unknown')</span>
            @endif
        </dd>
    </div>
</div>
