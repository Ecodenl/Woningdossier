{{--
    The resident has done the check and SmartTwin is working on the advice. The component below
    watches for the results and moves them on once they are in.
--}}
@extends('cooperation.frontend.layouts.tool')

@section('content')
    <livewire:cooperation.frontend.tool.simple-scan.my-plan.awaiting-advice :building="$building"
                                                                            :scan="$scan"
                                                                            :eventType="$eventType->value"/>
@endsection
