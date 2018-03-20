@extends('cooperation.tool.layout')

@section('step_title', __('woningdossier.cooperation.tool.general-data.title'))


@section('step_content')
    <form class="form-horizontal" method="POST" action="{{ route('cooperation.tool.wall-insulation.store', ['cooperation' => $cooperation]) }}">
        {{ csrf_field() }}
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group add-space{{ $errors->has('house_has_insulation') ? ' has-error' : '' }}">
                    <label for="house_has_insulation" class=" control-label">@lang('woningdossier.cooperation.tool.wall-insulation.')</label>

                    <select id="house_has_insulation" class="form-control" name="house_has_insulation" >
                        <option value="{{old('house_has_insulation')}}">@lang()</option>
                    </select>

                    @if ($errors->has('house_has_insulation'))
                        <span class="help-block">
                            <strong>{{ $errors->first('house_has_insulation') }}</strong>
                        </span>
                    @endif
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-sm-12">
                <div class="form-group add-space{{ $errors->has('additional_info') ? ' has-error' : '' }}">
                    <label for="additional-info" class=" control-label">@lang('woningdossier.cooperation.tool.general-data.data-about-usage.additional-info')</label>

                    <textarea id="additional-info" class="form-control" name="additional-info"> {{old('additional_info')}} </textarea>

                    @if ($errors->has('additional_info'))
                        <span class="help-block">
                            <strong>{{ $errors->first('additional_info') }}</strong>
                        </span>
                    @endif
                </div>
            </div>
        </div>



        <div class="row">
            <div class="col-md-12">
                <hr>
                <div class="form-group add-space">
                    <div class="">
                        <button type="submit" class="btn btn-primary">
                            @lang('default.buttons.store')
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection