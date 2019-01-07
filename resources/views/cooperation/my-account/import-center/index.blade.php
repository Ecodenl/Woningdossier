@extends('cooperation.my-account.layouts.app')

@section('my_account_content')
    <div class="container">
        <div class="row">
            <div class="col-md-10">
                <div class="panel panel-default">
                    <div class="panel-heading">@lang('woningdossier.cooperation.my-account.import-center.index.header')</div>

                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                @lang('woningdossier.cooperation.my-account.import-center.index.text')
                            </div>
                        </div>
                        @foreach($toolSettings as $toolSetting)
                            @if($toolSetting->hasChanged())
                                <form id="copy-input-{{$toolSetting->id}}" action="{{route('cooperation.import.copy')}}" method="post">
                                    <input type="hidden" name="input_source" value="{{$toolSetting->inputSource->short}}">
                                    {{csrf_field()}}
                                </form>
                            @endif
                        @endforeach

                        @foreach($toolSettings as $toolSetting)
                            <div class="row">
                                <div class="col-sm-12">
                                    @if($toolSetting->hasChanged())
                                        @component('cooperation.tool.components.alert', ['alertType' => 'success'])
                                            <div class="row">
                                                <a onclick="$('#copy-input-{{$toolSetting->id}}').submit()" class="btn btn-sm btn-primary pull-right">
                                                    @lang('woningdossier.cooperation.tool.general-data.coach-input.copy.title')
                                                </a>
                                                <a href="{{route('cooperation.my-account.import-center.set-compare-session', ['inputSourceShort' => $toolSetting->inputSource->short])}}" class="btn btn-sm btn-primary pull-right">
                                                    @lang('woningdossier.cooperation.my-account.import-center.index.show-differences')
                                                </a>
                                                @lang('woningdossier.cooperation.my-account.import-center.index.other-source',
                                                    ['input_source_name' => $toolSetting->inputSource->name
                                                ])
                                            </div>
                                        @endcomponent
                                    @else
                                        @component('cooperation.tool.components.alert', ['alertType' => 'warning'])
                                            Er zijn geen imports die gedaan kunnen worden
                                        @endcomponent
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
