@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('woningdossier.cooperation.admin.super-admin.translations.index.header')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <table id="table" class="table table-striped table-bordered compact nowrap table-responsive">
                        <thead>
                        <tr>
                            <th>@lang('woningdossier.cooperation.admin.super-admin.translations.index.table.columns.name')</th>
                            <th>@lang('woningdossier.cooperation.admin.super-admin.translations.index.table.columns.actions')</th>
                        </tr>
                        </thead>
                        <tbody>
                        {{--todo: this needs refactoring so we can just treat translations as translations instead of steps--}}
                        @foreach($mailLangFiles as $group =>  $translation)
                            <tr>
                                <td>{{$translation}}</td>
                                <td>
                                    <a class="btn btn-default" href="{{route('cooperation.admin.super-admin.translations.edit', [
                                                'group' => str_replace('/', '_', $group)
                                                ])}}">
                                        @lang('woningdossier.cooperation.admin.super-admin.translations.index.table.see')
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        @forelse($steps as $step)
                            <tr>
                                <td>{{optional($step->parentStep)->name.'/'. $step->name}}</td>
                                <td>
                                    <a class="btn btn-default" href="{{route('cooperation.admin.super-admin.translations.edit', ['group' => $step->short])}}">
                                        @lang('woningdossier.cooperation.admin.super-admin.translations.index.table.see')
                                    </a>
                                </td>
                            </tr>
                        @empty
                        @endforelse
                            <tr>
                                <td>@lang('woningdossier.cooperation.admin.super-admin.translations.index.table.main-translations')</td>
                                <td>
                                    <a class="btn btn-default" href="{{route('cooperation.admin.super-admin.translations.edit', ['group' => 'general'])}}">
                                        @lang('woningdossier.cooperation.admin.super-admin.translations.index.table.see')
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>@lang('woningdossier.cooperation.admin.super-admin.translations.index.table.pdf')</td>
                                <td>
                                    <a class="btn btn-default" href="{{route('cooperation.admin.super-admin.translations.edit', ['group' => 'pdf-user-report'])}}">
                                        @lang('woningdossier.cooperation.admin.super-admin.translations.index.table.see')
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>My plan</td>
                                <td>
                                    <a class="btn btn-default" href="{{route('cooperation.admin.super-admin.translations.edit', ['group' => 'my-plan'])}}">
                                        @lang('woningdossier.cooperation.admin.super-admin.translations.index.table.see')
                                    </a>
                                </td>
                            </tr>
                        <tr>
                            <td>Home</td>
                            <td>
                                <a class="btn btn-default" href="{{route('cooperation.admin.super-admin.translations.edit', ['group' => 'home'])}}">
                                    @lang('woningdossier.cooperation.admin.super-admin.translations.index.table.see')
                                </a>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function () {
            $('table').dataTable();
        });
    </script>
@endpush