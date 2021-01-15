@extends('cooperation.layouts.app')

@section('content')
    <div class="container" id="home">
        <div class="row">
            <div class="col-md-12">
                @if(!session('verified'))
                    @component('cooperation.tool.components.alert')
                        @lang('cooperation/auth/verify.success')
                    @endcomponent
                @endif
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="row">
                                    <div class="col-sm-12">
                                        {!! \App\Helpers\Translation::translate('home.start.dear-user.title') !!}
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="col-sm-12 text-center">
                                        <a class="start-button" href="{{route('cooperation.tool.general-data.index', ['cooperation' => $cooperation])}}">
                                            <img src="{{asset('images/start-icon.png')}}" class="h-150">
                                        </a>
                                    </div>
                                </div>

                            </div>
                            <div class="col-sm-6">
                                <img src="{{asset('images/pdf-main-images.jpg')}}" class="h-500 full-width home-img" alt="">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                {!! \App\Helpers\Translation::translate('home.start.your-cooperation.title', ['cooperation' => $cooperation->name]) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

