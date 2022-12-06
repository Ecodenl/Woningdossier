@extends('cooperation.frontend.layouts.app')

@section('main')
    <div class="w-screen h-screen flex justify-center items-center flex-col">
        <div class="bg-white rounded-3xl w-3/4 flex flex-wrap overflow-hidden min-h-15/20 max-h-19/20">
            <div class="p-10 xl:p-20 w-1/2 flex flex-col justify-between h-full overflow-auto">
                @if(session('verified'))
                    @component('cooperation.frontend.layouts.parts.alert', ['color' => 'blue-800'])
                        @lang('cooperation/auth/verify.success-log-in')
                    @endcomponent
                @endif
                {!! __('home.start.description') !!}


                <div class="flex justify-between space-x-2">
                    @foreach($cooperation->scans as $scan)
                        <a class="btn btn-purple"
                           href="{{\App\Services\Scans\ScanFlowService::init($scan, $building, $inputSource)->resolveInitialUrl()}}">
                            {{$scan->name}} {{$scan->short}}
                        </a>
                    @endforeach
                </div>
            </div>
            <div class="text-center w-1/2 relative bg-center bg-no-repeat bg-cover"
                 style="background-image: url('{{ asset('images/family.png') }}')">
                <i class="icon-hoomdossier-white absolute h-1/10 w-1/2 bottom-1/20" style="right: 12%"></i>
            </div>
        </div>
    </div>
@endsection