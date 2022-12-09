@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('cooperation/admin/cooperation/cooperation-admin/scans.index.title')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <form action="{{route('cooperation.admin.cooperation.cooperation-admin.scans.store')}}" method="post">
                        @csrf
                        <label for="">Selecteer hier welke scans je beschikbaar wilt stellen.</label>
                        <select class="form-control" name="scans[type]" id="scans">
                            @foreach($mapping as $type => $typeTranslation)
                                <option @if($currentScan === $type) selected @endif value="{{$type}}">{{$typeTranslation}}</option>
                            @endforeach
                        </select>

                        <div class="row mt-20">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-default">
                                    @lang('default.buttons.update')
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

