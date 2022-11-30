@extends('cooperation.frontend.layouts.tool')

@section('content')
    @livewire('cooperation.frontend.tool.quick-scan.my-plan.uploader', ['building' => $building])
@endsection