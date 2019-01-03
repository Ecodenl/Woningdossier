@extends('cooperation.admin.coach.layouts.app')

@section('coach_content')
    <div class="panel panel-default">
        <div class="panel-heading">@lang('woningdossier.cooperation.admin.coach.buildings.details.index.header')</div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <div class="alert alert-success show" role="alert">
                        {{ $building->city }} {{ $building->postal_code }}, {{ $building->street }} {{ $building->number }} {{ $building->extension }}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    @forelse($buildingNotes as $buildingNote)
                        <p class="pull-right">{{$buildingNote->created_at->format('Y-m-d H:i')}}</p>
                        <p>{{$buildingNote->note}}</p>
                        <hr>
                    @empty
                    @endforelse
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                @if($building instanceof \App\Models\Building)
                    <form action="{{route('cooperation.admin.coach.buildings.details.store')}}" method="post">
                        <input type="hidden" name="building_id" value="{{ $building->id }}">
                        {{csrf_field()}}
                            <textarea class="form-control" name="note"></textarea>
                            <button class="btn btn-primary pull-right" style="margin-top: 2em;">@lang('woningdossier.cooperation.admin.coach.buildings.details.index.form.submit')</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection


@push('js')

@endpush