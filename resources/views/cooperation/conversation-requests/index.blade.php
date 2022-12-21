@extends('cooperation.frontend.layouts.tool')

@section('content')
    <div class="flex flex-row flex-wrap w-full">
        <div class="w-full md:w-8/12 md:ml-2/12">
            <div class="flex flex-row flex-wrap w-full border border-solid border-blue-500 border-opacity-50 rounded-lg">
                <div class="flex flex-row flex-wrap w-full items-center bg-white px-5 py-2 rounded-lg">
                    <form class="form-horizontal" method="POST"
                          action="{{ route('cooperation.conversation-requests.store', compact('cooperation')) }}">
                        @csrf

                        <h2 class="heading-2">
                            {{$title}}
                        </h2>
                        @if(! is_null($measureApplicationName))
                            <input type="hidden" value="{{ $measureApplicationName }}" name="measure_application_name">
                        @endif
                        <input type="hidden" name="request_type" value="{{$requestType}}">

                        @component('cooperation.frontend.layouts.components.form-group', [
                            'label' => __('conversation-requests.index.form.message'),
                            'withInputSource' => false,
                            'id' => 'message',
                            'inputName' => 'message',
                        ])
                            <textarea name="message" class="form-input"
                                      placeholder="@lang('conversation-requests.index.form.message')"
                            >{{old('message')}}</textarea>
                        @endcomponent

                        <div class="w-full flex flex-row flex-wrap">
                            <div class="w-full flex flex-wrap">
                                <a href="{{ route('cooperation.frontend.tool.simple-scan.my-plan.index', compact('scan')) }}"
                                   class="btn btn-outline-orange flex items-center justify-center mr-2">
                                    @lang('default.buttons.cancel')
                                </a>
                                <button type="submit" class="btn btn-green flex items-center justify-center">
                                    @lang('conversation-requests.index.form.submit')
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection