
@extends('cooperation.admin.cooperation.coordinator.layouts.app')

@section('coordinator_content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('woningdossier.cooperation.admin.cooperation.coordinator.connect-to-coach.create.header', ['firstName' => $privateMessage->getSender($privateMessage->id)->first_name, 'lastName' => $privateMessage->getSender($privateMessage->id)->last_name])

        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <form action="{{route('cooperation.admin.cooperation.coordinator.connect-to-coach.store')}}" method="post"  >
                        {{csrf_field()}}
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group" {{ $errors->has('title') ? ' has-error' : '' }}>
                                    <label for="title">@lang('woningdossier.cooperation.admin.cooperation.coordinator.connect-to-coach.create.form.title')</label>
                                    <input type="text" class="form-control" name="title" placeholder="@lang('woningdossier.cooperation.admin.cooperation.coordinator.connect-to-coach.create.form.title')">
                                    @if ($errors->has('title'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('title') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group" {{ $errors->has('message') ? ' has-error' : '' }}>
                                    <label for="message">@lang('woningdossier.cooperation.admin.cooperation.coordinator.connect-to-coach.create.form.message-to-coach.label')</label>
                                    <textarea name="message" id="" class="form-control">@lang('woningdossier.cooperation.admin.cooperation.coordinator.connect-to-coach.create.form.message-to-coach.placeholder')</textarea>
                                    @if ($errors->has('message'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('message') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group" {{ $errors->has('coach') ? ' has-error' : '' }}>
                                    <label for="coach">@lang('woningdossier.cooperation.admin.cooperation.coordinator.connect-to-coach.create.form.select-coach')</label>
                                    <select name="coach" class="coach form-control" id="coach">
                                        @foreach($coaches as $coach)
                                            <option @if(old('coach') == $coach->id) selected @endif value="{{$coach->id}}">{{$coach->first_name ." ". $coach->last_name}}</option>
                                        @endforeach
                                    </select>

                                    @if ($errors->has('coach'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('coach') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <button class="btn btn-primary btn-block" type="submit">@lang('woningdossier.cooperation.admin.cooperation.coordinator.connect-to-coach.create.form.submit') <span class="glyphicon glyphicon-plus"></span></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection



@push('css')
    <link rel="stylesheet" href="{{asset('css/select2/select2.min.css')}}">
    @push('js')
        <script src="{{asset('js/select2.js')}}"></script>

        <script>

            $('form').disableAutoFill();

            $('form').on('submit', function () {
                if (confirm('@lang('woningdossier.cooperation.admin.cooperation.coordinator.connect-to-coach.create.form.submit-warning', ['firstName' => $privateMessage->getSender($privateMessage->id)->first_name, 'lastName' => $privateMessage->getSender($privateMessage->id)->last_name])')) {

                } else {
                    event.preventDefault();

                }
            });


            $('.collapse').on('shown.bs.collapse', function(){
                $(this).parent().find(".glyphicon-plus").removeClass("glyphicon-plus").addClass("glyphicon-minus");
            }).on('hidden.bs.collapse', function(){
                $(this).parent().find(".glyphicon-minus").removeClass("glyphicon-minus").addClass("glyphicon-plus");
            });


            $(document).ready(function () {

                $(".coach").select2({
                    placeholder: "@lang('woningdossier.cooperation.admin.cooperation.coordinator.connect-to-coach.create.form.select-coach')",
                    maximumSelectionLength: Infinity
                });
            });
        </script>
    @endpush

