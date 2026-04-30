@extends('cooperation.admin.layouts.app', [
    'panelTitle' => __('woningdossier.cooperation.admin.cooperation.coordinator.side-nav.add-user')
])

@section('content')
    @php
        $cooperationToCheck = isset($cooperationToManage) && $cooperationToManage instanceof \App\Models\Cooperation
            ? $cooperationToManage : $cooperation;
        $country = strtolower($cooperationToCheck->country);
        $supportsLvBag = $cooperationToCheck->getCountry()->supportsApi(\App\Enums\ApiImplementation::LV_BAG);
    @endphp
    <div x-data="{duplicateData: []}">
        @component('cooperation.layouts.components.alert', [
            'color' => 'blue-900',
            'dismissible' => false,
            'display' => false,
            'attr' => 'style="display: none;" x-on:duplicates-checked.window="$el.style.display = $event.detail.showDuplicateError ? \'\' : \'none\'; ' . ($supportsLvBag ? '"' : 'duplicateData = $event.detail.addresses || [];"'),
        ])
            @lang("auth.register.form.duplicate-address.{$country}")
            <ul x-show="duplicateData.length > 0">
                <template x-for="data in duplicateData">
                    <li x-text="data"></li>
                </template>
            </ul>
        @endcomponent
    </div>

    <div class="flex w-full"
         x-data="register(
             '{{route('cooperation.check-existing-email', ['cooperation' => $cooperation, 'forCooperation' => $cooperationToManage ?? $cooperation])}}',
             '{{ old('scans.type', $currentScan) }}'
         )">
        <form class="w-full flex flex-wrap"
              @if(isset($cooperationToManage))
                  action="{{route('cooperation.admin.super-admin.cooperations.cooperation-to-manage.users.store', compact('cooperation', 'cooperationToManage'))}}"
              @else
                  action="{{route('cooperation.admin.users.store', compact('cooperation'))}}"
              @endif
              method="POST">
            @csrf

            <h3 class="w-full heading-4 mb-4">
                @lang('cooperation/admin/buildings.edit.account-user-info-title')
            </h3>

            @component('cooperation.frontend.layouts.components.form-group', [
                    'withInputSource' => false,
                    'label' => __('users.column-translations.first_name'),
                    'class' => 'w-full -mt-5 lg:w-1/2 lg:pr-3',
                    'inputName' => 'users.first_name',
                    'attr' => 'x-show="! alreadyMember && ! noBuilding"',
                ])
                <input class="form-input" type="text" name="users[first_name]" value="{{ old('users.first_name') }}">
            @endcomponent
            @component('cooperation.frontend.layouts.components.form-group', [
                'withInputSource' => false,
                'label' => __('users.column-translations.last_name'),
                'class' => 'w-full -mt-5 lg:w-1/2 lg:pl-3',
                'inputName' => 'users.last_name',
                'attr' => 'x-show="! alreadyMember && ! noBuilding"',
            ])
                <input class="form-input" type="text" name="users[last_name]" value="{{ old('users.last_name') }}">
            @endcomponent

            @component('cooperation.frontend.layouts.components.form-group', [
                'withInputSource' => false,
                'label' => __('accounts.column-translations.email'),
                'class' => 'w-full -mt-5 lg:w-1/2 lg:pr-3',
                'inputName' => 'accounts.email',
            ])
                <input class="form-input" type="text" name="accounts[email]" value="{{ old('accounts.email') }}"
                       x-on:change="checkEmail($el)">
                <p class="text-red w-full text-left mb-2" x-show="showEmailWarning" x-cloak>
                    @lang('auth.register.form.possible-wrong-email')
                </p>
                <p class="text-blue-800 w-full text-left mb-2" x-show="alreadyMember" x-cloak>
                    @lang('cooperation/admin/users.create.form.already-member')
                </p>
                <p class="text-blue-800 w-full text-left mb-2" x-show="noBuilding" x-cloak>
                    @lang('cooperation/admin/users.create.form.no-building')
                </p>
                <p class="text-blue-800 w-full text-left mb-2" x-show="emailExists" x-cloak>
                    @lang('cooperation/admin/users.create.form.e-mail-exists')
                </p>
            @endcomponent
            @component('cooperation.frontend.layouts.components.form-group', [
                    'withInputSource' => false,
                    'label' => __('users.column-translations.phone_number'),
                    'class' => 'w-full -mt-5 lg:w-1/2 lg:pl-3',
                    'inputName' => 'users.phone_number',
                    'attr' => 'x-show="! alreadyMember && ! noBuilding"',
                ])
                <input class="form-input" type="text" name="users[phone_number]" value="{{ old('users.phone_number') }}">
            @endcomponent

            @component('cooperation.frontend.layouts.components.form-group', [
                'class' => 'w-full -mt-5 lg:w-1/2 lg:pr-3',
                'label' => __('cooperation/admin/users.create.form.roles'),
                'id' => 'role-select',
                'inputName' => "roles",
                'withInputSource' => false,
                'attr' => 'x-show="! alreadyMember && ! noBuilding"',
            ])
                @component('cooperation.frontend.layouts.components.alpine-select', ['withSearch' => true])
                    <select multiple id="role-select" class="form-input hidden" name="roles[]"
                            @cannot('editAny', $userCurrentRole) disabled @endcannot>
                        <option selected disabled>@lang('cooperation/admin/users.create.form.select-role')</option>
                        @foreach($roles as $role)
                            @can('view', [$role, Hoomdossier::user(), HoomdossierSession::getRole(true)])
                                <option value="{{$role->id}}"
                                        @if(in_array($role->id, old('roles', []))) selected @endif
                                >
                                    {{$role->human_readable_name}}
                                </option>
                            @endcan
                        @endforeach
                    </select>
                @endcomponent
            @endcomponent

            @component('cooperation.frontend.layouts.components.form-group', [
                'class' => 'w-full -mt-5 lg:w-1/2 lg:pl-3',
                'label' => __('cooperation/admin/users.create.form.select-coach'),
                'id' => 'associated-coaches',
                'inputName' => "coach_id",
                'withInputSource' => false,
                'attr' => 'x-show="! alreadyMember && ! noBuilding"',
            ])
                @component('cooperation.frontend.layouts.components.alpine-select', ['withSearch' => true])
                    <select id="associated-coaches" class="form-input hidden" name="coach_id">
                        <option selected disabled>@lang('cooperation/admin/users.create.form.select-coach')</option>
                        @foreach($coaches as $coach)
                            <option value="{{$coach->id}}"
                                    @if(old('coach_id') == $coach->id) selected @endif
                            >
                                {{$coach->getFullName()}}
                            </option>
                        @endforeach
                    </select>
                @endcomponent
            @endcomponent

            {{-- TODO: Contact ID? --}}

            {{-- Scan availability & small measures --}}
            <div class="w-full flex flex-wrap" x-show="! alreadyMember && ! noBuilding">
                {{-- Scan type select --}}
                @component('cooperation.frontend.layouts.components.form-group', [
                    'withInputSource' => false,
                    'label' => __('cooperation/admin/users.create.form.scan-availability.label'),
                    'id' => 'scans-type',
                    'class' => 'w-full lg:w-1/2 lg:pr-3',
                    'inputName' => 'scans.type',
                ])
                    <p class="text-sm text-gray-600 mb-2">
                        @lang('cooperation/admin/users.create.form.scan-availability.description')
                    </p>
                    @component('cooperation.frontend.layouts.components.alpine-select')
                        <select class="form-input hidden" name="scans[type]" id="scans-type" x-model="selectedScan">
                            @foreach($mapping as $type => $typeTranslation)
                                <option @if($currentScan === $type) selected @endif value="{{ $type }}">{{ $typeTranslation }}</option>
                            @endforeach
                        </select>
                    @endcomponent
                @endcomponent

                {{-- Small measures checkboxes --}}
                <div class="form-group w-full lg:w-1/2 lg:pl-3">
                    <div class="form-header">
                        <label class="form-label max-w-16/20">
                            @lang('cooperation/admin/users.create.form.small-measures.label')
                        </label>
                    </div>
                    <div class="w-full">
                        <p class="text-sm text-gray-600 mb-2">
                            @lang('cooperation/admin/users.create.form.small-measures.description')
                        </p>

                        @foreach(['quick-scan' => __('cooperation/admin/cooperation/cooperation-admin/scans.form.small-measures.quick-scan'), 'lite-scan' => __('cooperation/admin/cooperation/cooperation-admin/scans.form.small-measures.lite-scan')] as $scanShort => $scanName)
                            @php
                                $isLiteScan = $scanShort === \App\Models\Scan::LITE;
                                $smCoopEnabled = $smallMeasuresSettings[$scanShort]['cooperation_enabled'] ?? true;
                                $oldSmValue = $isLiteScan ? '1' : old('scans.small_measures_enabled.' . $scanShort, $smCoopEnabled ? '1' : '0');
                            @endphp
                            <div class="flex items-center mb-3"
                                 x-show="selectedScan === '{{ $scanShort }}' || selectedScan === 'both-scans'"
                                 x-cloak>
                                <label class="flex items-center {{ $isLiteScan ? 'cursor-not-allowed opacity-60' : 'cursor-pointer' }}">
                                    <input type="hidden" name="scans[small_measures_enabled][{{ $scanShort }}]" value="{{ $isLiteScan ? '1' : '0' }}">
                                    <input type="checkbox"
                                           name="scans[small_measures_enabled][{{ $scanShort }}]"
                                           value="1"
                                           class="form-checkbox h-5 w-5 text-green-600"
                                           @if($isLiteScan) checked disabled @elseif($oldSmValue === '1') checked @endif>
                                    <span class="ml-2">
                                        {{ $scanName }}: @lang('cooperation/admin/cooperation/cooperation-admin/scans.form.small-measures.label')
                                        @if($isLiteScan)
                                            <span class="text-sm text-gray-500 italic">
                                                (@lang('cooperation/admin/cooperation/cooperation-admin/scans.form.small-measures.always-required'))
                                            </span>
                                        @endif
                                    </span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <h3 class="w-full heading-4 my-4" x-show="! alreadyMember && ! noBuilding">
                @lang('cooperation/admin/buildings.edit.address-info-title')
            </h3>

            <div class="w-full" x-show="! alreadyMember && ! noBuilding">
                @include('cooperation.layouts.address', [
                    'withLabels' => true,
                    'checks' => [
                        'correct_address', 'duplicates',
                    ],
                    'supportsLvBag' => $supportsLvBag,
                ])
            </div>

            <div class="w-full mt-5" x-show="! alreadyMember && ! noBuilding">
                <button class="btn btn-green flex justify-center items-center w-full" type="submit"
                        x-on:click="setTimeout(() => {submitted = true;});"
                        x-bind:disabled="submitted || alreadyMember || noBuilding">
                    @lang('cooperation/admin/users.create.form.submit')
                    <i class="w-3 h-3 icon-plus-purple ml-1"></i>
                </button>
            </div>
        </form>
    </div>
@endsection