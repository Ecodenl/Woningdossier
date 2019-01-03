@extends('cooperation.admin.cooperation.cooperation-admin.layouts.app')

@section('cooperation_admin_content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.steps.index.header')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <table id="table" class="table table-striped table-bordered compact nowrap table-responsive">
                        <thead>
                        <tr>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.steps.index.table.columns.name')</th>
                            <th>@lang('woningdossier.cooperation.admin.cooperation.cooperation-admin.steps.index.table.columns.active')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($steps as $step)
                        <tr>
                            <td>{{$step->name}}</td>
                                <td>
                                    <input data-active="{{$cooperation->isStepActive($step) ? 'on' : 'off'}}" class="toggle-active" data-step-id="{{$step->id}}"  type="checkbox"  data-toggle="toggle"  data-on="Actief" data-off="Niet actief">
                                </td>
                            </tr>
                        @empty
                        @endforelse
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link href="{{asset('css/bootstrap-toggle.min.css')}}" rel="stylesheet">
@push('js')
    <script src="{{asset('js/bootstrap-toggle.min.js')}}"></script>

    <script>
        $(document).ready(function () {
            $('#table').dataTable();

            var toggleActive = $('.toggle-active');

            $(toggleActive).each(function (index, value) {
                $(this).bootstrapToggle($(this).data('active'));
            });

            toggleActive.change(function () {
                console.log($(this));
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "post",
                    url: '{{route('cooperation.admin.cooperation.cooperation-admin.steps.set-active')}}',
                    data: {
                        step_active: $(this).prop('checked'),
                        step_id: $(this).data('step-id')
                    }
                }).done(function () {
                    console.log('bier!');
                })
            });
        });

    </script>
@endpush
