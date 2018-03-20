@extends('cooperation.tool.layout')

@section('step_title', __('woningdossier.cooperation.tool.title'))

@section('step_content')
    <h2>Get started</h2>

    <a href="{{ route('cooperation.tool.general-data.index', ['cooperation' => $cooperation ]) }}" class="btn btn-primary btn-lg">Start here</a>
@endsection