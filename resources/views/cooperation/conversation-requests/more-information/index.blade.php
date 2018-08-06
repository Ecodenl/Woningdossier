@extends('cooperation.layouts.app')


@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">@lang('woningdossier.cooperation.conversation-request.more-information.index.header', ['measure' => $measureApplication->measure_name])</div>

                    <div class="panel-body">
                        <form class="form-horizontal" method="POST" action="{{ route('cooperation.conversation-request.more-information.store', ['cooperation' => $cooperation]) }}">
                            {{ csrf_field() }}

                            <h2>@lang('woningdossier.cooperation.conversation-request.more-information.index.header', ['measure' => $measureApplication->measure_name])</h2>
                            <p>@lang('woningdossier.cooperation.conversation-request.more-information.index.text')</p>


                            <div class="form-group{{ $errors->has('message') ? ' has-error' : '' }}">
                                <div class="col-sm-12">
                                    <textarea name="message" class="form-control">@lang('woningdossier.cooperation.conversation-request.more-information.index.form.message', ['measure' => $measureApplication->measure_name]) </textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-12 ">
                                    <button type="submit" class="btn btn-primary">
                                        @lang('woningdossier.cooperation.conversation-request.more-information.index.form.submit')
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
