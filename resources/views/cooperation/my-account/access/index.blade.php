@extends('cooperation.my-account.layouts.app')

@section('my_account_content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('woningdossier.cooperation.my-account.messages.index.header')
        </div>

        <div class="panel-body">
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
        });
    </script>
@endpush



