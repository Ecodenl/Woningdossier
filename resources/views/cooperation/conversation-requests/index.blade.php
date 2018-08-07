@extends('cooperation.layouts.app')


@section('content')
    <style>

    </style>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">@lang('woningdossier.cooperation.conversation-requests.index.header')</div>

                    <div class="panel-body">
                        <form class="form-horizontal" method="POST" action="{{ route('cooperation.conversation-requests.store', ['cooperation' => $cooperation]) }}">
                            {{ csrf_field() }}

                            @if($measureApplicationName == "")
                                <h2>@lang('woningdossier.cooperation.conversation-requests.index.form.no-measure-application-name-title')</h2>
                            @else
                                <h2>@lang('woningdossier.cooperation.conversation-requests.index.form.title', ['measure_application_name' => $measureApplicationName])</h2>
                            @endif

                            <input type="hidden" value="{{$measureApplicationName}}" name="measure_application_name">

                            <div class="form-group {{ $errors->has('action') ? ' has-error' : '' }}">
                                <div class="col-sm-12">
                                    <label for="">@lang('woningdossier.cooperation.conversation-requests.index.form.take-action')</label>
                                    <div class="input-group" id="take-action">
                                        <input disabled placeholder="@lang('woningdossier.cooperation.conversation-requests.index.form.take-action')" type="text" class="form-control disabled" aria-label="...">
                                        <div class="input-group-btn">
                                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                @lang('woningdossier.cooperation.conversation-requests.index.form.action')
                                                <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                                @foreach(__('woningdossier.cooperation.conversation-requests.index.form.options') as $value => $label)
                                                    <li>
                                                        <label>
                                                            <input name="action" @if(isset($selectedOption) && $value == $selectedOption) checked @endif value="{{$value}}" type="radio">
                                                            {{$label}}
                                                        </label>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div><!-- /btn-group -->
                                    </div><!-- /input-group -->
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
                                    <textarea name="message" class="form-control">@lang('woningdossier.cooperation.conversation-requests.index.form.message')</textarea>
                                    
                                    @if ($errors->has('message'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('message') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('agreement') ? ' has-error' : '' }}">
                                <div class="col-sm-12">
                                    <label for="">
                                        <input name="agreement" type="checkbox">
                                        @lang('woningdossier.cooperation.conversation-requests.index.form.agreement')
                                    </label>
                                    @if ($errors->has('agreement'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('agreement') }}</strong>
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
    <script type="text/javascript">
        $(document).ready(function () {

            // put the label text from the selected option inside the input for ux
            var takeAction = $('#take-action');
            var input = $(takeAction).find('input.form-control');
            var dropdown = $(takeAction).find('input[type=radio]');
            var inputPrefix = '@lang('woningdossier.cooperation.conversation-requests.index.form.selected-option')';

            $(dropdown).change(function () {
                var radioLabel = $('input[type=radio]:checked').parent().text().trim().toLowerCase();

                $(input).val();
                $(input).val(inputPrefix +' '+ radioLabel);
            });

            $(dropdown).trigger('change');

            // when the form gets submited check if the user agreed with the agreement
            // if so submit, else do nuthing
            $('form').on('submit', function () {

                if ($('input[name=agreement]').is(':checked')  == false) {

                    if (confirm('Weet u zeker dat u geen toesteming wilt geven ?')) {

                    } else {
                       event.preventDefault();

                    }
                }
            })

        })
    </script>

@endpush