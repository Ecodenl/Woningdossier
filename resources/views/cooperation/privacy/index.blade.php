@extends('cooperation.layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="panel tab-pane tab-pane panel-default" id="privacy">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">
                            {!! \App\Helpers\Translation::translate('home.privacy.description.title', ['cooperation' => $cooperation->name]) !!}
                        </div>
                    </div>
                    <br>
                </div>
            </div>
        </div>
    </div>
@endsection
