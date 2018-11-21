@extends('cooperation.layouts.app')


@section('content')
    <style>

    </style>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">@lang('woningdossier.cooperation.conversation-requests.edit.header', ['request_type' => __('woningdossier.cooperation.conversation-requests.edit.form.'.$myOpenCoachConversationRequest->request_type)])</div>

                    <div class="panel-body">
                        <form class="form-horizontal" method="POST" action="{{ route('cooperation.conversation-requests.update', ['cooperation' => $cooperation]) }}">
                            {{ csrf_field() }}

                            <h2>@lang('woningdossier.cooperation.conversation-requests.edit.header', ['request_type' => __('woningdossier.cooperation.conversation-requests.edit.form.'.$myOpenCoachConversationRequest->request_type)])</h2>
                            <p>@lang('woningdossier.cooperation.conversation-requests.edit.text')</p>

                            <div class="form-group {{ $errors->has('action') ? ' has-error' : '' }}">
                                <div class="col-sm-12">
                                    <label for="">@lang('woningdossier.cooperation.conversation-requests.edit.form.take-action')</label>
                                    <div class="input-group" id="take-action">
                                        <input disabled placeholder="@lang('woningdossier.cooperation.conversation-requests.edit.form.take-action')" type="text" class="form-control disabled" aria-label="...">
                                        <div class="input-group-btn">
                                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                @lang('woningdossier.cooperation.conversation-requests.edit.form.action')
                                                <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                                @foreach(__('woningdossier.cooperation.conversation-requests.edit.form.options') as $value => $label)
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
                                    <label for="message">@lang('woningdossier.cooperation.conversation-requests.edit.form.message')</label>
                                    <textarea id="message" name="message" class="form-control">@if(isset($intendedMessage)){{ $intendedMessage }}@elseif(isset($myOpenCoachConversationRequest)){{ $myOpenCoachConversationRequest->message }} @else @lang('woningdossier.cooperation.conversation-requests.coach.edit.form.message') @endif </textarea>

                                    @if ($errors->has('message'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('message') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('allow_access') ? ' has-error' : '' }}">
                                <div class="col-sm-12">
                                    <label for="">
                                        <input name="allow_access" type="checkbox" @if((old('allow_access') && old('allow_access') == 'on') || $myOpenCoachConversationRequest->allow_access)checked="checked"@endif>
                                        @lang('woningdossier.cooperation.conversation-requests.edit.form.allow_access')
                                    </label>
                                    @if ($errors->has('allow_access'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('allow_access') }}</strong>
                                        </span>
                                    @endif
                                    <p>@lang('woningdossier.cooperation.conversation-requests.edit.text')</p>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-12 ">
                                    <button type="submit" class="btn btn-primary">
                                        @lang('woningdossier.cooperation.conversation-requests.edit.form.update')
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
            var inputPrefix = '@lang('woningdossier.cooperation.conversation-requests.edit.form.selected-option')';


            var coachConversationTranslation = '{{ __('woningdossier.cooperation.conversation-requests.edit.form.options.'.\App\Models\PrivateMessage::REQUEST_TYPE_COACH_CONVERSATION) }}';

            $(dropdown).change(function () {


                var radioLabel = $('input[type=radio]:checked').parent().text().trim();

                if (coachConversationTranslation !== radioLabel) {
                    window.location = '{{ url('/aanvragen') }}' + '/' + $('input[type=radio]:checked').val()
                }

                // we lower the case after the check is done, otherwise it would fail in any case
                radioLabel.toLowerCase();

                $(input).val();
                $(input).val(inputPrefix +' '+ radioLabel);
            });

            $(dropdown).trigger('change');

            // when the form gets submited check if the user agreed with the allow_access
            // if so submit, else do nuthing
            $('form').on('submit', function () {

                if ($('input[name=allow_access]').is(':checked')  === false) {

                    if (confirm('Weet u zeker dat u geen toesteming wilt geven?')) {

                    } else {
                       event.preventDefault();

                    }
                }
            })

        })
    </script>

@endpush