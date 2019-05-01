@extends('cooperation.my-account.layouts.app')

@section('my_account_content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('woningdossier.cooperation.my-account.access.index.header')
        </div>

        <div class="panel-body">
        @if($conversationRequests->isNotEmpty())
            <div class="row">
                <div class="col-sm-12">
                    <form id="allow-access-form" action="{{route('cooperation.my-account.access.allow-access')}}" method="post">
                        {{csrf_field()}}
                        <div class="form-group {{ $errors->has('allow_access') ? ' has-error' : '' }}">
                            <label for="allow_access">
                                <input id="allow_access" name="allow_access" type="checkbox"
                                       @if(old('allow_access') && old('allow_access') == 'on' || $conversationRequests->contains('allow_access', true))
                                            checked="checked"
                                        @endif>
                                @lang('woningdossier.cooperation.conversation-requests.index.form.allow_access', ['cooperation' => \App\Models\Cooperation::find(\App\Helpers\HoomdossierSession::getCooperation())->name])
                            </label>
                            @if ($errors->has('allow_access'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('allow_access') }}</strong>
                                </span>
                            @endif
                            <p>@lang('woningdossier.cooperation.conversation-requests.index.text')</p>
                        </div>
                    </form>
                </div>
            </div>
            @endif
            <div class="row">
                <div class="col-sm-12">
                    <table id="table" class="table table-striped table-responsive table-bordered compact nowrap">
                        <thead>
                        <tr>
                            <th>@lang('woningdossier.cooperation.my-account.access.index.table.columns.coach')</th>
                            <th>@lang('woningdossier.cooperation.my-account.access.index.table.columns.actions')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($buildingPermissions as $i => $buildingPermission)
                            <form id="revoke-access-{{$buildingPermission->id}}" action="{{route('cooperation.messages.participants.revoke-access')}}" method="post">
                                {{csrf_field()}}
                                <input type="hidden" name="user_id" value="{{$buildingPermission->user_id}}">
                                <input type="hidden" name="building_owner_id" value="{{$buildingPermission->building_id}}">
                            </form>
                            <tr>
                                <td>{{ $buildingPermission->user->getFullName() }}</td>
                                <td>
                                    <a data-form-id="revoke-access-{{$buildingPermission->id}}" class="revoke-access btn btn-danger"><i class="glyphicon glyphicon-trash"></i></a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function () {
            $('.revoke-access').click(function () {
                if (confirm('Weet u zeker dat u deze gebruiker de toegang wilt ontzetten ?')) {
                    var formId = $(this).data('form-id');
                    $('form#'+formId).submit();
                }
            });

            $('#allow_access').change(function () {
                // if access gets turned of, we want to show them a alert
                // else we dont!
                if ($(this).prop('checked') === false) {
                    if (confirm('Weet u zeker dat u de toegang wilt ontzeggen voor elk gekoppelde coach ?')) {
                        $('#allow-access-form').submit();
                    } else {
                        // otherwise this may seems weird, so on cancel. we check the box again.
                        $(this).prop('checked', true);
                    }
                }  else {
                    $('#allow-access-form').submit();
                }
            });
        });
    </script>
@endpush



