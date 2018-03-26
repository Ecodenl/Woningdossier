@extends('cooperation.tool.layout')

@section('step_title', __('woningdossier.cooperation.tool.wall-insulation.intro.title'))


@section('step_content')
    <form class="form-horizontal" method="POST"
          action="{{ route('cooperation.tool.wall-insulation.store', ['cooperation' => $cooperation]) }}">
        {{ csrf_field() }}
        <div id="floor-insulation">
            <div class="row">
                <div class="col-sm-12">
                    <h4 style="margin-left: -5px">@lang('woningdossier.cooperation.tool.floor-insulation.title')</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group add-space{{ $errors->has('floor_insulation') ? ' has-error' : '' }}">
                        <label for="floor_insulation" class=" control-label"><i data-toggle="collapse"
                                                                                data-target="#floor-insulation-info"
                                                                                class="glyphicon glyphicon-info-sign glyphicon-padding"></i>@lang('woningdossier.cooperation.tool.floor-insulation.floor-insulation')
                        </label>

                        <select id="floor_insulation" class="form-control" name="floor_insulation">
                            @foreach($insulations->take(5) as $insulation)
                                <option @if(old('floor_insulation') == $insulation->id) selected
                                        @endif value="{{$insulation->id}}">{{$insulation->name}}</option>
                            @endforeach
                        </select>

                        @if ($errors->has('floor_insulation'))
                            <span class="help-block">
                                <strong>{{ $errors->first('floor_insulation') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="col-sm-12">
                    <div class="form-group add-space">
                        <div id="floor-insulation-info" class="collapse alert alert-info remove-collapse-space">
                            I would like to have some help full information right here !
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div id="has-no-crawlspace"
                         class="form-group add-space{{ $errors->has('has_crawlspace') ? ' has-error' : '' }}">
                        <label for="has_crawlspace" class=" control-label">
                            <i data-toggle="collapse" data-target="#has-crawlspace-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                            @lang('woningdossier.cooperation.tool.floor-insulation.has-crawlspace.title')
                        </label>

                        <select id="has_crawlspace" class="form-control" name="has_crawlspace">
                            @foreach(__('woningdossier.cooperation.option') as $option)
                                <option @if(old('has_crawlspace') == $option) selected
                                        @endif value="{{$option}}">{{$option}}</option>
                            @endforeach
                        </select>

                        <div class="col-sm-12">
                            <div class="form-group add-space">
                                <div id="has-crawlspace-info" class="collapse alert alert-info remove-collapse-space">
                                    I would like to have some help full information right here !
                                </div>
                            </div>
                        </div>

                        @if ($errors->has('has_crawlspace'))
                            <span class="help-block">
                                <strong>{{ $errors->first('has_crawlspace') }}</strong>
                            </span>
                        @endif

                        <span id="no-crawlspace-error" class="help-block" style="display: none;">
                            <strong>@lang('woningdossier.cooperation.tool.floor-insulation.has-crawlspace.no-crawlspace')</strong>
                        </span>
                    </div>
                </div>
            </div>
            <div id="has-no-crawlspace-wrapper">
                <div class="row">
                    <div class="col-sm-12">
                        <div id="has-crawlspace-access" class="form-group add-space {{ $errors->has('crawlspace_access') ? ' has-error' : '' }}">
                            <label for="crawlspace_access" class=" control-label">
                                <i data-toggle="collapse" data-target="#crawlspace-access-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                                @lang('woningdossier.cooperation.tool.floor-insulation.crawlspace-access.title')
                            </label>

                            <select id="crawlspace_access" class="form-control" name="crawlspace_access">
                                @foreach(__('woningdossier.cooperation.option') as $option)
                                    <option @if(old('has_crawlspace') == $option) selected
                                            @endif value="{{$option}}">{{$option}}</option>
                                @endforeach
                            </select>

                            <div class="col-sm-12">
                                <div class="form-group add-space">
                                    <div id="crawlspace-access-info"
                                         class="collapse alert alert-info remove-collapse-space">
                                        I would like to have some help full information right here !
                                    </div>
                                </div>
                            </div>

                            @if ($errors->has('crawlspace_access'))
                                <span class="help-block">
                                <strong>{{ $errors->first('crawlspace_access') }}</strong>
                            </span>
                            @endif

                            <span id="crawlspace-no-access-error" class="help-block" style="display: none;">
                            <strong>@lang('woningdossier.cooperation.tool.floor-insulation.crawlspace-access.no-access')</strong>
                        </span>

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group add-space{{ $errors->has('crawlspace_height') ? ' has-error' : '' }}">
                            <label for="crawlspace_height" class=" control-label">
                                <i data-toggle="collapse" data-target="#crawlspace-height-info" class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                                @lang('woningdossier.cooperation.tool.floor-insulation.crawlspace-height')
                            </label>

                            <select id="crawlspace_height" class="form-control" name="crawlspace_height">
                                @foreach($crawlHeights as $crawlHeight)
                                    <option @if(old('crawlspace_height') == $crawlHeight->id) selected
                                            @endif value="{{$crawlHeight->id}}">{{$crawlHeight->name}}</option>
                                @endforeach
                            </select>

                            @if ($errors->has('crawlspace_height'))
                                <span class="help-block">
                                <strong>{{ $errors->first('crawlspace_height') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="form-group add-space">
                            <div id="crawlspace-height-info" class="collapse alert alert-info remove-collapse-space">
                                I would like to have some help full information right here !
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group add-space{{ $errors->has('floor_surface') ? ' has-error' : '' }}">

                            <label for="floor_surface" class=" control-label">
                                <i data-toggle="collapse" data-target="#floor-surface-info"
                                   class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                                @lang('woningdossier.cooperation.tool.floor-insulation.floor-surface-comparable', ['surface' => 'WAARDE'])
                            </label>
                            <div class="input-group">
                                <span class="input-group-addon">m<sup>2</sup></span>
                                <input type="text" name="floor_surface" class="form-control" value="653">
                            </div>
                            @if ($errors->has('floor_surface'))
                                <span class="help-block">
                                <strong>{{ $errors->first('floor_surface') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="form-group add-space">
                            <div id="floor-surface-info" class="collapse alert alert-info remove-collapse-space">
                                I would like to have some help full information right here !
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group add-space{{ $errors->has('floor_best_insulation') ? ' has-error' : '' }}">

                            <label for="floor_best_insulation" class=" control-label">
                                <i data-toggle="collapse" data-target="#floor-best-insulation-info"
                                   class="glyphicon glyphicon-info-sign glyphicon-padding"></i>
                                @lang('woningdossier.cooperation.tool.floor-insulation.floor-best-insulation')
                            </label>
                            <div class="input-group">
                                <span class="input-group-addon">m<sup>2</sup></span>
                                <input type="text" name="floor_best_insulation" class="form-control disabled"
                                       disabled="" value="653">
                            </div>
                            @if ($errors->has('floor_best_insulation'))
                                <span class="help-block">
                                <strong>{{ $errors->first('floor_best_insulation') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="form-group add-space">
                            <div id="floor-best-insulation-info"
                                 class="collapse alert alert-info remove-collapse-space">
                                I would like to have some help full information right here !
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <hr>
                <div class="form-group add-space">
                    <div class="">
                        <a class="btn btn-success pull-left"
                           href="{{route('cooperation.tool.general-data.index', ['cooperation' => $cooperation])}}">@lang('default.buttons.prev')</a>
                        <button type="submit" disabled class="disabled btn btn-primary pull-right">
                            @lang('default.buttons.next')
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('js')
    <script>
        $(document).change(function () {
            // check if the is painted button is yes
            if ($('#is-painted').is(':checked')) {
                $('#painted-options').show();
            } else {
                $('#painted-options').hide();
            }
        });

        $('#crawlspace_access').change(function () {
            if ($(this).val() == "Nee") {
                $('#has-crawlspace-access').addClass('has-error');
                $('#crawlspace-no-access-error').show();
            } else {
                $('#has-crawlspace-access').removeClass('has-error');
                $('#crawlspace-no-access-error').hide();
            }
        });

        $('#has_crawlspace').change(function () {
            if ($(this).val() == "Nee") {
                $('#has-no-crawlspace').addClass('has-error');
                $('#no-crawlspace-error').show();
                // This will hide all the part's that have no use when there is no crawlspace
                $('#has-no-crawlspace-wrapper').hide();
            } else {
                $('#has-no-crawlspace').removeClass('has-error');
                $('#no-crawlspace-error').hide();
                $('#has-no-crawlspace-wrapper').show();
            }
        })
    </script>
@endpush

