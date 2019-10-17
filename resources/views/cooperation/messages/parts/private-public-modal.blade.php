@if(isset($buildings) && $buildings instanceof \Illuminate\Support\Collection)
    @foreach($buildings as $building)
        <div id="private-public-{{$building->id}}" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <p>@lang('woningdossier.cooperation.chat.modal.text')</p>
                    </div>
                    <div class="modal-footer">
                        <div class="pull-left">
                            <a href="{{route($publicRoute, ['buildingId' => $building->id])}}" id="participate-public" class="btn btn-warning">@lang('woningdossier.cooperation.chat.modal.public')</a>
                        </div>
                        <div class="pull-right">
                            <a href="{{route($privateRoute, ['buildingId' => $building->id])}}" id="participate-private" class="btn btn-success">@lang('woningdossier.cooperation.chat.modal.private')</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endif