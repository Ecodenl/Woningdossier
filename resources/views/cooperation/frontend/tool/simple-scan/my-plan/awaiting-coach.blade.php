{{--
    A coach started an advice in SmartTwin and has not delivered a plan yet: the resident's woonplan
    is locked, and there is nothing to show yet. See App\Services\SmartTwin\WoonplanLock.
--}}
@extends('cooperation.frontend.layouts.tool')

@section('content')
    <div class="w-full">
        <h4 class="heading-4 mb-5">@lang('cooperation/frontend/tool.my-plan.lock.awaiting.title')</h4>
        {{-- Only the resident gets here (WoonplanLock::appliesTo), so their questions go to their messages. --}}
        <p>{!! __('cooperation/frontend/tool.my-plan.lock.awaiting.text', ['link' => route('cooperation.my-account.messages.edit', compact('cooperation'))]) !!}</p>
    </div>
@endsection
