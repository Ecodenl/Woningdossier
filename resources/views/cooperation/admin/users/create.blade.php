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
             {
                 @foreach($allScans as $scan)
                     @php
                         $isCoopScan = in_array($scan->id, $cooperationScanIds);
                         $oldVal = old('scans_enabled.' . $scan->short, $isCoopScan ? '1' : '0');
                     @endphp
                     '{{ $scan->short }}': {{ $oldVal === '1' ? 'true' : 'false' }},
                 @endforeach
             }
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

            {{-- Scan availability & small measures checkboxes --}}
            <div class="w-full flex flex-wrap" x-show="! alreadyMember && ! noBuilding">
                {{-- Scan availability --}}
                <div class="w-full lg:w-1/2 lg:pr-3 mb-4">
                    <label class="form-label">
                        @lang('cooperation/admin/users.create.form.scan-availability.label')
                    </label>
                    <p class="text-sm text-gray-600 mb-2">
                        @lang('cooperation/admin/users.create.form.scan-availability.description')
                    </p>

                    @foreach($allScans as $scan)
                        @php
                            $isCooperationScan = in_array($scan->id, $cooperationScanIds);
                        @endphp
                        <div class="flex items-center mb-2">
                            <input type="hidden" name="scans_enabled[{{ $scan->short }}]" value="0">
                            <input type="checkbox"
                                   id="scan-enabled-{{ $scan->short }}"
                                   name="scans_enabled[{{ $scan->short }}]"
                                   value="1"
                                   class="form-checkbox mr-2"
                                   x-model="scansEnabled['{{ $scan->short }}']"
                            >
                            <label for="scan-enabled-{{ $scan->short }}">
                                {{ $scan->name }}
                                @if(! $isCooperationScan)
                                    <span class="text-sm text-gray-500">
                                        (@lang('cooperation/admin/users.create.form.scan-availability.not-in-cooperation'))
                                    </span>
                                @endif
                            </label>
                        </div>
                    @endforeach

                    @error('scans_enabled')
                        <p class="text-red text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Small measures - only visible for enabled scans --}}
                <div class="w-full lg:w-1/2 lg:pl-3 mb-4"
                     x-show="hasAnyScanEnabled" x-cloak>
                    <label class="form-label">
                        @lang('cooperation/admin/users.create.form.small-measures.label')
                    </label>
                    <p class="text-sm text-gray-600 mb-2">
                        @lang('cooperation/admin/users.create.form.small-measures.description')
                    </p>

                    @foreach($allScans as $scan)
                        @php
                            $smCoopEnabled = $smallMeasuresSettings[$scan->short]['cooperation_enabled'] ?? true;
                            $smLocked = $scan->isLiteScan();
                            $oldSmValue = $smLocked ? '1' : old('small_measures_override.' . $scan->short, $smCoopEnabled ? '1' : '0');
                        @endphp
                        <div class="flex items-center mb-2"
                             x-show="scansEnabled['{{ $scan->short }}']" x-cloak>
                            <input type="hidden" name="small_measures_override[{{ $scan->short }}]" value="{{ $smLocked ? '1' : '0' }}">
                            <input type="checkbox"
                                   id="small-measures-{{ $scan->short }}"
                                   name="small_measures_override[{{ $scan->short }}]"
                                   value="1"
                                   class="form-checkbox mr-2"
                                   @checked($oldSmValue === '1')
                                   @disabled($smLocked)
                            >
                            <label for="small-measures-{{ $scan->short }}" @class(['opacity-50' => $smLocked])>
                                {{ $scan->name }}
                                <span class="text-sm text-gray-500">
                                    @if($smLocked)
                                        (@lang('cooperation/admin/users.create.form.small-measures.always-enabled'))
                                    @else
                                        (@lang($smCoopEnabled
                                            ? 'cooperation/admin/users.create.form.small-measures.cooperation-enabled'
                                            : 'cooperation/admin/users.create.form.small-measures.cooperation-disabled'))
                                    @endif
                                </span>
                            </label>
                        </div>
                    @endforeach
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
                <p class="text-red text-sm mb-2" x-show="! hasAnyScanEnabled" x-cloak>
                    @lang('cooperation/admin/users.create.form.scan-availability.at-least-one')
                </p>
                <button class="btn btn-green flex justify-center items-center w-full" type="submit"
                        x-on:click="setTimeout(() => {submitted = true;});"
                        x-bind:disabled="submitted || alreadyMember || noBuilding || ! hasAnyScanEnabled">
                    @lang('cooperation/admin/users.create.form.submit')
                    <i class="w-3 h-3 icon-plus-purple ml-1"></i>
                </button>
            </div>
        </form>
    </div>
@endsection