@extends('cooperation.layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <form class="form-horizontal" method="POST" action="{{ route('cooperation.conversation-requests.store', ['cooperation' => $cooperation]) }}">
                            {{ csrf_field() }}

                            <h2>{{$title}}</h2>
                            @isset($measureApplicationName)
                            <input type="hidden" value="{{ $measureApplicationName }}" name="measure_application_name">
                            @endisset
                            <input type="hidden" name="request_type" value="{{$requestType}}">


                            <div class="form-group{{ $errors->has('message') ? ' has-error' : '' }}">
                                <div class="col-sm-12">
                                    <label for="">@lang('conversation-requests.index.form.message')</label>
                                    <textarea name="message" class="form-control" placeholder="@lang('conversation-requests.index.form.message')">{{old('message')}}</textarea>

                                    @if ($errors->has('message'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('message') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>


                            <div class="form-group">
                                <div class="col-md-12 ">
                                    <button type="submit" class="btn btn-primary">
                                        @if(isset($privateMessage))
                                            @lang('conversation-requests.index.form.update')
                                        @else
                                            @lang('conversation-requests.index.form.submit')
                                        @endif
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection