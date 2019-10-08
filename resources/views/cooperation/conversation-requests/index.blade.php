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

                            @if($measureApplicationName == "")
                                <h2>@lang('woningdossier.cooperation.conversation-requests.index.form.no-measure-application-name-title')</h2>
                            @else
                                <h2>@lang('woningdossier.cooperation.conversation-requests.index.form.title', ['measure_application_name' => $measureApplicationName])</h2>
                            @endif

                            <input type="hidden" value="{{ $measureApplicationName }}" name="measure_application_name">

                            <div class="form-group {{ $errors->has('action') ? ' has-error' : '' }}">
                                <div class="col-sm-12">
                                    <select name="action" id="take-action" class="form-control">
                                        <option value="" disabled="disabled" selected="selected">
                                            @lang('woningdossier.cooperation.conversation-requests.index.form.selected-option')
                                        </option>
                                        @foreach(__('woningdossier.cooperation.conversation-requests.index.form.options') as $value => $label)
                                            <option @if(((isset($selectedOption)) && $value == $selectedOption) || old('action') == $value ) checked @endif value="{{ $value }}" type="radio">
                                                {{$label}}
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

                            <div class="form-group{{ $errors->has('message') ? ' has-error' : '' }}">
                                <div class="col-sm-12">
                                    <label for="">@lang('woningdossier.cooperation.conversation-requests.index.form.message')</label>
                                    <textarea name="message" class="form-control" placeholder="@lang('woningdossier.cooperation.conversation-requests.index.form.message')">{{old('message')}}</textarea>

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
                                        @lang('woningdossier.cooperation.conversation-requests.index.form.allow_access', ['cooperation' => \App\Models\Cooperation::find(\App\Helpers\HoomdossierSession::getCooperation())->name])
                                    </label>
                                    @if ($errors->has('allow_access'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('allow_access') }}</strong>
                                        </span>
                                    @endif
                                    <p>@lang('woningdossier.cooperation.conversation-requests.index.text')</p>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-12 ">
                                    <button type="submit" class="btn btn-primary">
                                        @if(isset($privateMessage))
                                            @lang('woningdossier.cooperation.conversation-requests.index.form.update')
                                        @else
                                            @lang('woningdossier.cooperation.conversation-requests.index.form.submit')
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
            var helpPlaceholderText = '@lang('woningdossier.cooperation.conversation-requests.index.form.selected-option')';
            $('#take-action').select2({
                placeholder: helpPlaceholderText
            });

            // when the form gets submited check if the user agreed with the allow_access
            // if so submit, else do nothing
            $('form').on('submit', function () {

                if ($('input[name=allow_access]').is(':checked')  == false) {

                    if (confirm('@lang('woningdossier.cooperation.conversation-requests.index.form.are-you-sure')')) {

                    } else {
                        return false;
                        event.preventDefault();
                    }
                }
            })

        })

    </script>

@endpush