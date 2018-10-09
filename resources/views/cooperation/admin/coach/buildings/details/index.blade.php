@extends('cooperation.admin.coach.layouts.app')

@section('coach_content')
    <?php $mostRecentBuildingNote = $buildingNotes->last() ?>
    <div class="panel panel-default">
        <div class="panel-heading">@lang('woningdossier.cooperation.admin.coach.buildings.details.index.header')</div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <div class="alert alert-success alert-dismissible show" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        {{$mostRecentBuildingNote->city}} {{$mostRecentBuildingNote->postal_code}}, {{$mostRecentBuildingNote->street}} {{$mostRecentBuildingNote->number}} {{$mostRecentBuildingNote->extension}}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-8">
                    @forelse($buildingNotes as $buildingNote)
                        <p>{{$buildingNote->note}}</p>
                        <hr>
                    @empty
                    @endforelse
                </div>
                @if($building instanceof \App\Models\Building)
                    <div class="col-sm-4">
                        <div class="row">
                            <form action="{{route('cooperation.admin.coach.buildings.details.store')}}" method="post">
                                <input type="hidden" name="building_id" value="{{$building->id}}">
                                {{csrf_field()}}
                                <div class="col-sm-12">
                                    <textarea class="form-control" name="note"></textarea>
                                </div>
                                <br>
                                <br>
                                <br>
                                <div class="col-sm-12">
                                    <button class="btn btn-primary">@lang('woningdossier.cooperation.admin.coach.buildings.details.index.form.submit')</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection


@push('js')

@endpush