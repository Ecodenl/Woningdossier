@extends('cooperation.admin.layouts.app', [
    'menu' => false,
    'panelTitle' => __('woningdossier.cooperation.admin.choose-roles.header')
])

@section('content')
    <div class="w-full">
        <p>@lang('woningdossier.cooperation.admin.choose-roles.text')</p>
    </div>
    <br>
    <div class="w-full grid grid-rows-1 grid-cols-4 grid-flow-row gap-4">
        @foreach($user->roles as $i => $role)
            <a href="{{ route('cooperation.admin.switch-role', ['role' => $role->name]) }}"
               class="no-underline hover:shadow-center-green">
                <div class="rounded-lg border border-blue border-solid flex flex-wrap justify-center items-center p-8">
                    <i class="icon-lg icon-account-circle w-full"></i>

                    <h3 class="heading-4 mt-2">
                        {{ $role->human_readable_name }}
                    </h3>
                </div>
            </a>
        @endforeach
    </div>
@endsection
