@extends('cooperation.layouts.app')

@push('css')
    <link rel="stylesheet" href="{{asset('css/select2/select2.min.css')}}">
@endpush
@section('content')
    <style>

    </style>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <form class="form-horizontal" method="POST" action="{{ route('cooperation.conversation-requests.store', ['cooperation' => $cooperation]) }}">
                            {{ csrf_field() }}

                            <h2>{{$title}}</h2>
                            <input type="hidden" value="{{ $measureApplicationName }}" name="measure_application_name">

                            @if($shouldShowOptionList)
                            <div class="form-group {{ $errors->has('action') ? ' has-error' : '' }}">
                                <div class="col-sm-12">
                                    <select name="action" id="take-action" class="form-control">
                                        {{--A empty option is needed to allow the placeholder to be shown, as long as the value is empty select2 will not display it. --}}
                                        <option value="">-</option>
                                        @foreach(__('conversation-requests.request-types') as $requestType => $requestTypeTranslation)
                                            <option @if(old('action', $requestType) == $selectedOption) selected="selected" @endif value="{{ $requestTypeTranslation }}">
                                                {{$requestTypeTranslation}}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('action'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('action') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            @else
                                <input type="hidden" name="action" value="{{$selectedOption}}">
                            @endif

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

                            <div class="form-group {{ $errors->has('allow_access') ? ' has-error' : '' }}">
                                <div class="col-sm-12">
                                    <label for="allow_access">
                                        <input id="allow_access" name="allow_access" type="checkbox" @if(old('allow_access') && old('allow_access') == 'on')checked="checked"@endif>
                                        @lang('conversation-requests.index.form.allow_access', ['cooperation' => \App\Models\Cooperation::find(\App\Helpers\HoomdossierSession::getCooperation())->name])
                                    </label>
                                    @if ($errors->has('allow_access'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('allow_access') }}</strong>
                                        </span>
                                    @endif
                                    <p>@lang('conversation-requests.index.text')</p>
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

@push('js')
    <script src="{{asset('js/select2.js')}}"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            var helpPlaceholderText = '@lang('conversation-requests.index.form.selected-option')';
            console.log(helpPlaceholderText);
            $('#take-action').select2({
                allowClear: true,
                placeholder: helpPlaceholderText
            });

            // when the form gets submited check if the user agreed with the allow_access
            // if so submit, else do nothing
            $('form').on('submit', function (event) {
                if ($('input[name=allow_access]').is(':checked') === false) {

                    if (!confirm('@lang('conversation-requests.index.form.are-you-sure')')) {
                        event.preventDefault();
                        return false;
                    }
                }
            });
        });
    </script>

@endpush