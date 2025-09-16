@extends('cooperation.admin.layouts.app', [
    'panelTitle' => __('woningdossier.cooperation.admin.super-admin.cooperations.cooperation-to-manage.home.index.header', ['cooperation' => $cooperationToManage->name])
])

@section('content')
    <div class="w-full flex flex-wrap">
        <div class="w-full xl:w-1/3 mb-2 xl:mb-0 px-3">
            <div class="flex justify-between items-center rounded-lg bg-green-700 p-4 text-white">
                <i class="icon-xl icon-person-white"></i>

                <div>
                    <div class="font-bold text-xxl text-right">{{$residentCount}}</div>
                    <div>@lang('woningdossier.cooperation.admin.super-admin.cooperations.cooperation-to-manage.home.index.resident-count')</div>
                </div>
            </div>
        </div>
        <div class="w-full xl:w-1/3 mb-2 xl:mb-0 px-3">
            <div class="flex justify-between items-center rounded-lg bg-green-700 p-4 text-white">
                <i class="icon-xl icon-person-white"></i>

                <div>
                    <div class="font-bold text-xxl text-right">{{$coachCount}}</div>
                    <div>@lang('woningdossier.cooperation.admin.super-admin.cooperations.cooperation-to-manage.home.index.coach-count')</div>
                </div>
            </div>
        </div>
        <div class="w-full xl:w-1/3 mb-2 xl:mb-0 px-3">
            <div class="flex justify-between items-center rounded-lg bg-green-700 p-4 text-white">
                <i class="icon-xl icon-person-white"></i>

                <div>
                    <div class="font-bold text-xxl text-right">{{$coordinatorCount}}</div>
                    <div>@lang('woningdossier.cooperation.admin.super-admin.cooperations.cooperation-to-manage.home.index.coordinator-count')</div>
                </div>
            </div>
        </div>

        <div class="w-2/3"></div>

        <div class="w-full xl:w-1/3 px-3 mt-4">
            <div class="border border-solid border-blue-500 border-opacity-50 rounded-lg">
                <div class="w-full divide-y divide-blue-500/50">
                    <div class="p-4 flex items-center">
                        <i class="icon-sm icon-tools mr-2"></i>
                        <h3 class="heading-5 inline-block font-normal">
                            Settings
                        </h3>
                    </div>
                    <div class="p-4">
                        @can('delete', $cooperationToManage)
                            <div class="w-full flex items-center" x-data="modal">
                                <button class="btn btn-red" x-on:click="toggle()">
                                    @lang('woningdossier.cooperation.admin.super-admin.cooperations.index.destroy')
                                </button>
                                @component('cooperation.frontend.layouts.components.modal', [
                                    'header' => "Verwijder {$cooperationToManage->name}",
                                    'class' => 'confirm-modal',
                                ])
                                    <form action="{{route('cooperation.admin.super-admin.cooperations.destroy', ['cooperationToDestroy' => $cooperationToManage])}}"
                                          method="POST">
                                        @csrf
                                        @method('DELETE')

                                        <p class="text-red w-full mb-4">
                                            @lang('woningdossier.cooperation.admin.super-admin.cooperations.index.modal.text', ['cooperation' => $cooperationToManage->name])
                                        </p>

                                        <div class="w-full flex flex-wrap justify-between">
                                            <button type="submit" class="btn btn-red">
                                                @lang('woningdossier.cooperation.admin.super-admin.cooperations.index.modal.destroy')
                                            </button>
                                            <button type="button" class="btn btn-outline-purple" x-on:click="close()">
                                                @lang('woningdossier.cooperation.admin.super-admin.cooperations.index.modal.cancel')
                                            </button>
                                        </div>
                                    </form>
                                @endcomponent
                            </div>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection