@extends('cooperation.frontend.layouts.tool')

@section('step_title', $step->name)

@section('content')
    @livewire('cooperation.frontend.tool.expert-scan.form', ['step' => $step, 'cooperation' => $cooperation])
@endsection

