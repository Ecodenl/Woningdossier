@extends('cooperation.admin.layouts.app', [
    'panelTitle' => __('cooperation/admin/super-admin/smart-twin-solutions.index.title'),
])

@section('content')
    <div class="w-full">
        <p class="mb-3">
            @lang('cooperation/admin/super-admin/smart-twin-solutions.index.description')
        </p>

        @if($kinds->isEmpty())
            <p class="text-red">
                @lang('cooperation/admin/super-admin/smart-twin-solutions.index.empty')
            </p>
        @else
            <p class="mb-5 @if($undecided > 0) text-red @endif">
                {{ trans_choice('cooperation/admin/super-admin/smart-twin-solutions.index.undecided', $undecided, ['count' => $undecided]) }}
            </p>

            <form action="{{ route('cooperation.admin.super-admin.smart-twin-solutions.couple') }}" method="POST">
                @csrf
                @method('PUT')

                @foreach($kinds as $kind => $solutions)
                    <table class="table fancy-table w-full mb-8">
                        <thead>
                            <tr>
                                <th class="w-2/3">
                                    {{ $kind ?: __('cooperation/admin/super-admin/smart-twin-solutions.index.table.no-kind') }}
                                    <small class="text-gray">({{ $solutions->count() }})</small>
                                </th>
                                <th class="w-1/3">
                                    <div class="flex w-full">
                                    <select class="form-input js-bulk" data-kind="{{ $loop->index }}">
                                        <option value="">
                                            @lang('cooperation/admin/super-admin/smart-twin-solutions.index.table.bulk')
                                        </option>
                                        <option value="{{ $notCoupled }}">
                                            @lang('cooperation/admin/super-admin/smart-twin-solutions.index.table.not-coupled')
                                        </option>
                                        @foreach($measureApplications as $stepName => $measures)
                                            <optgroup label="{{ $stepName }}">
                                                @foreach($measures as $measure)
                                                    <option value="{{ $measure->id }}">{{ $measure->short }} — {{ $measure->name }}</option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody data-kind="{{ $loop->index }}">
                            @foreach($solutions as $solution)
                                @php
                                    $currentChoice = array_key_exists($solution->id, $couplings)
                                        ? (is_null($couplings[$solution->id]) ? $notCoupled : (string) $couplings[$solution->id])
                                        : '';
                                    $selected = (string) old("couplings.{$solution->id}", $currentChoice);
                                @endphp
                                <tr>
                                    <td class="whitespace-normal">
                                        {{ $solution->name }}
                                        @if($withdrawn->has($solution->id))
                                            <small class="text-red">
                                                @lang('cooperation/admin/super-admin/smart-twin-solutions.index.table.withdrawn')
                                            </small>
                                        @endif
                                        <br>
                                        {{-- A solution id is one long token without spaces; left to itself it
                                             stretches the column and squeezes the select next to it. --}}
                                        <small class="text-gray break-all">{{ $solution->external_id }}</small>
                                    </td>
                                    <td>
                                        {{-- form-input is sized to fill a flex parent (width: 1%; flex-auto),
                                             so on its own in a cell it collapses to nothing. --}}
                                        <div class="flex w-full">
                                        <select name="couplings[{{ $solution->id }}]" class="form-input js-coupling">
                                            <option value="" @selected('' === $selected)>
                                                @lang('cooperation/admin/super-admin/smart-twin-solutions.index.table.undecided')
                                            </option>
                                            <option value="{{ $notCoupled }}" @selected($notCoupled === $selected)>
                                                @lang('cooperation/admin/super-admin/smart-twin-solutions.index.table.not-coupled')
                                            </option>
                                            @foreach($measureApplications as $stepName => $measures)
                                                <optgroup label="{{ $stepName }}">
                                                    @foreach($measures as $measure)
                                                        <option value="{{ $measure->id }}" @selected((string) $measure->id === $selected)>
                                                            {{ $measure->short }} — {{ $measure->name }}
                                                        </option>
                                                    @endforeach
                                                </optgroup>
                                            @endforeach
                                        </select>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endforeach

                <div class="w-full mt-5">
                    <button class="btn btn-green">
                        @lang('default.buttons.save')
                    </button>
                </div>
            </form>
        @endif
    </div>
@endsection

@push('js')
    <script type="module" nonce="{{ $cspNonce }}">
        // Setting every product of a kind at once: the products of one kind are usually the same
        // measure to us, so this is the difference between one pick and a dozen.
        document.querySelectorAll('.js-bulk').forEach(function (bulk) {
            bulk.addEventListener('change', function () {
                if ('' === bulk.value) {
                    return;
                }

                document
                    .querySelectorAll('tbody[data-kind="' + bulk.dataset.kind + '"] .js-coupling')
                    .forEach(function (select) {
                        select.value = bulk.value;
                    });

                bulk.value = '';
            });
        });
    </script>
@endpush
