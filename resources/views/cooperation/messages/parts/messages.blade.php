{{-- Legacy support --}}
@if(($tailwind ?? false))
    <div class="w-full flex flex-wrap flex-row">
        <div class="w-full">
            <div class="panel-collapse " id="collapseOne">
                <ul class="chat divide-y divide-y-blue-500 divide-dashed space-y-2">
                    {{$slot}}
                </ul>
            </div>
        </div>
    </div>
@else
    <div class="row">
        <div class="col-md-12">
            <div class="panel-collapse " id="collapseOne">
                <ul class="chat">
                    {{$slot}}
                </ul>
            </div>
        </div>
    </div>
@endif
