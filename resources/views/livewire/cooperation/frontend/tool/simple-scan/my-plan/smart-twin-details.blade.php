{{--
    What SmartTwin says about an advice: the solution with its product details, and per insulated
    element how it is now and how the measure leaves it. See App\Services\SmartTwin\SmartTwinAdviceDetails.
    SmartTwin's numbers are shown as given, unrounded.
--}}
@php
    $exact = fn ($value) => is_numeric($value) ? \App\Helpers\NumberFormatter::formatExact($value, 0) : $value;
    $insulatedElements = ['facade', 'floor', 'crawlspace', 'roof'];
@endphp
<div class="space-y-3 mb-4">
    <h6 class="heading-6">@lang('cooperation/frontend/tool.my-plan.smart-twin-details.title')</h6>

    @foreach($details['solutions'] ?? [] as $solution)
        <div>
            <p class="font-bold">{{ $solution['name'] ?? '' }}</p>
            @if(! empty($solution['details']))
                <ul class="list-disc pl-5">
                    @foreach($solution['details'] as $id => $detail)
                        <li>{{ $detail['name'] ?? $id }}: {{ $exact($detail['value'] ?? null) }} {{ $detail['unit'] ?? '' }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endforeach

    @foreach($details['elements'] ?? [] as $element => $state)
        @continue(! in_array($element, $insulatedElements, true) || empty($state['before']['summary']))
        <div>
            <p class="font-bold">
                @lang("cooperation/frontend/tool.my-plan.smart-twin-details.elements.{$element}")
                @if(! empty($state['shape']))
                    ({{ __("cooperation/frontend/tool.my-plan.smart-twin-details.shapes.{$state['shape']}") }})
                @endif
            </p>
            @foreach(['now' => $state['before'], 'after' => $state['after']] as $moment => $situation)
                <p>
                    @lang("cooperation/frontend/tool.my-plan.smart-twin-details.{$moment}"):
                    @if(is_null($situation))
                        @lang('cooperation/frontend/tool.my-plan.smart-twin-details.unchanged')
                    @else
                        @lang('cooperation/frontend/tool.my-plan.smart-twin-details.rc-value')
                        {{ $exact($situation['summary']['rc_value'] ?? null) ?? '–' }} m²K/W,
                        @lang('cooperation/frontend/tool.my-plan.smart-twin-details.area')
                        {{ $exact($situation['summary']['area'] ?? null) }} m²
                    @endif
                </p>
            @endforeach
            @if(! empty($state['insulated_area']))
                <p>@lang('cooperation/frontend/tool.my-plan.smart-twin-details.insulated-area'): {{ $exact($state['insulated_area']) }} m²</p>
            @endif
        </div>
    @endforeach
</div>
