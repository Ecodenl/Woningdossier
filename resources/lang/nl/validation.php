<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | De following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => 'De :attribute moeten geaccepteerd worden.',
    'active_url'           => 'De :attribute is geen geldige URL.',
    'after'                => 'De :attribute moet een datum zijn na :date.',
    'after_or_equal'       => 'De :attribute moet een datum zijn van :date of later.',
    'alpha'                => 'De :attribute mag alleen letters bevatten.',
    'alpha_dash'           => 'De :attribute mag alleen letters, cijfers en streepjes bevatten.',
    'alpha_num'            => 'De :attribute mag alleen letters en cijfers bevatten.',
    'array'                => 'De :attribute moet een array zijn',
    'before'               => 'De :attribute moet een datum zijn voor :date.',
    'before_or_equal'      => 'De :attribute moet een datum zijn van :date of eerder.',
    'between'              => [
        'numeric' => ' :attribute moet tussen :min en :max liggen.',
        'file'    => 'De :attribute moet tussen :min en :max kilobytes groot zijn.',
        'string'  => 'De :attribute moet tussen :min en :max tekens zijn.',
        'array'   => 'De :attribute moet tussen :min en :max items bevatten.',
    ],
    'boolean'              => 'De :attribute moet waar of onwaar zijn.',
    'confirmed'            => 'De :attribute bevestiging is niet correct.',
    'hash_check'           => ' :attribute is niet correct.',
    'date'                 => 'Vul een geldige datum in',
    'date_format'          => 'De :attribute is niet volgens het formaat :format.',
    'different'            => 'De :attribute en :other mogen niet hetzelfde zijn.',
    'digits'               => 'De :attribute moet :digits cijfers zijn.',
    'digits_between'       => 'De :attribute moet tussen :min en :max cijfers bevatten.',
    'dimensions'           => 'De :attribute heeft ongeldige afmetingen.',
    'distinct'             => 'De :attribute heeft een dubbele waarde.',
    'email'                => 'De :attribute moet een geldig e-mailadres zijn.',
    'exists'               => 'De geselecteerde waarde is ongeldig.',
    'file'                 => 'De :attribute moet een bestand zijn.',
    'filled'               => ' :attribute veld moet ingevuld zijn.',
    'image'                => 'De :attribute moet een image zijn.',
    'in'                   => 'De geselecteerde :attribute is ongeldig.',
    'in_array'             => ' :attribute veld is geen optie in :other.',
    'integer'              => 'De :attribute moet een afgerond getal zijn.',
    'ip'                   => 'De :attribute moet een geldig IP adres zijn.',
    'ipv4'                 => 'De :attribute moet een geldig IPv4 adres zijn.',
    'ipv6'                 => 'De :attribute moet een geldig IPv6 adres zijn.',
    'json'                 => 'De :attribute moet een geldige JSON string zijn.',
    'max'                  => [
        'numeric' => ' :attribute mag niet groter dan :max zijn.',
        'file'    => 'De :attribute mag niet groter dan :max kilobytes zijn.',
        'string'  => 'De :attribute mag niet meer dan :max karakters bevatten.',
        'array'   => 'De :attribute mag niet meer dan :max items bevatten.',
    ],
    'mimes'                => 'De :attribute moet een bestand zijn met type: :values.',
    'mimetypes'            => 'De :attribute moet een bestand zijn met type: :values.',
    'min'                  => [
        'numeric' => ':attribute moet tenminste :min zijn.',
        'file'    => ':attribute moet tenminste :min kilobytes groot zijn.',
        'string'  => 'De :attribute moet tenminste :min karakters bevatten.',
        'array'   => 'De :attribute moet tenminste :min items bevatten.',
    ],
    'not_in'               => 'De geselecteerde :attribute is ongeldig.',
    'numeric'              => ' :attribute moet een getal bevatten',
    'present'              => 'De :attribute moet gevuld zijn.',
    'phone_number'         => 'Telefoonnummer bevat geen geldig telefoonnummer.',
    'postal_code'          => 'De opgegeven postcode is ongeldig.',
    'house_number'         => ' opgegeven huisnummer is ongeldig.',
    'regex'                => ':attribute formaat is ongeldig.',
    'required'             => ':attribute veld is verplicht.',
    'required_if'          => ':attribute veld is verplicht wanneer :other :value is.',
    'required_unless'      => ':attribute veld is verplicht tenzij :other in :values zit.',
    'required_with'        => ':attribute veld is verplicht wanneer :values aanwezig is.',
    'required_with_all'    => ':attribute veld is verplicht wanneer :values aanwezig is.',
    'required_without'     => ':attribute veld is verplicht wanneer :values niet aanwezig is.',
    'required_without_all' => ':attribute veld is verplicht wanneer geen van :values aanwezig is.',
    'same'                 => 'De :attribute en :other moeten overeenkomen.',
    'size'                 => [
        'numeric' => 'De :attribute moet :size zijn.',
        'file'    => 'De :attribute moet :size kilobytes zijn.',
        'string'  => 'De :attribute moet :size karakters zijn.',
        'array'   => 'De :attribute moet :size items bevatten.',
    ],
    'string'               => 'De :attribute moet een tekst zijn.',
    'timezone'             => 'De :attribute moet een geldig tijdzone zijn.',
    'unique'               => 'De :attribute is al geregistreerd.',
    'uploaded'             => 'is mislukt om :attribute failed to upload.',
    'url'                  => 'De :attribute formaat is ongeldig.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'needs_to_be_lower_or_same_as' => 'Dit veld moet gelijk of kleiner zijn dan het veld :attribute',
        'alpha_space' => ':attribute mag alleen letters en spaties bevatten.',
        'surface' => 'Dit veld is verplicht als u een dak type heeft gekozen.',
        'is-user-member-of-cooperation' => 'De opgegeven gebruiker is geen lid van de huidige cooperatie',
        'needs-to-be-filled' => 'Dit veld moet gevuld zijn',

        'building_roof_types.flat.insulation_roof_surface' => [
            'needs_to_be_lower_or_same_as' => '"Te isoleren oppervlakte van het plate dak" moet gelijk of kleiner zijn dan het veld "Dakoppervlak platte dak"'
        ],

        'building_roof_types.pitched.insulation_roof_surface' => [
            'needs_to_be_lower_or_same_as' => '"Te isoleren oppervlakte van het hellende dak" moet gelijk of kleiner zijn dan het veld "Dakoppervlak hellend dak"'
        ],

        'building_insulated_glazings.*.m2' => [
            'required' => '"Hoeveel m2 glas wilt u vervangen?" is een verplicht veld.',
            'numeric' => '"Hoeveel m2 glas wilt u vervangen?" moet een getal bevatten.',
            'min' => '"Hoeveel m2 glas wilt u vervangen?" moet tenminste :min zijn.',
        ],
        'building_insulated_glazings.*.windows' => [
            'required' => '"aantal te vervangen ruiten?" is een verplicht veld.',
            'numeric' => '"aantal te vervangen ruiten?" moet een getal bevatten.',
            'min' => '"aantal te vervangen ruiten?" moet tenminste :min zijn.',
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | De following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    // note, when a key needs a wildcard add it to the custom array.
    // A wildcard wont work in the attributes array but will in the custom array, needs more work but at least we dont need to hardcode the * numbers.
    'attributes' => [
        'account.current_password' => 'huidig wachtwoord',
        'account.password' => 'wachtwoord',
        'account.password_confirmation' => 'wachtwoord bevestigen',

        // general data
        'building_layers' => __('general-data.building-type.how-much-building-layers.title'),
        'resident_count' => 'bewoners aantal',
        'surface' => __('general-data.building-type.what-user-surface.title'),
        'thermostat_high' => 'temperatuur van de thermostaat op hoge stand',
        'thermostat_low' => 'temperatuur van de thermostaat op lage stand',
        'window_surface' => 'totale raamopperlake van de woning',
        'build_year' => 'bouwjaar',
        '6.extra.year' => 'jaartal',
        '7.extra.year' => 'jaartal',
        'service.7' => 'aantal zonnepanelen',

        // roof insulation
        'building_roof_types.pitched.roof_surface' => __('roof-insulation.current-situation.pitched-roof-surface.title'),
        'building_roof_types.pitched.insulation_roof_surface' => __('roof-insulation.current-situation.insulation-pitched-roof-surface.title'),

        'building_roof_types.flat.roof_surface' => __('roof-insulation.current-situation.flat-roof-surface.title'),
        'building_roof_types.flat.insulation_roof_surface' => __('roof-insulation.current-situation.insulation-flat-roof-surface.title'),

        'building_roof_types.flat.extra.zinc_replaced_date' =>  __('roof-insulation.current-situation.zinc-replaced.title'),
        'building_roof_types.flat.extra.bitumen_replaced_date' =>  __('roof-insulation.current-situation.bitumen-insulated.title'),

        // wall insulation
        'wall_surface' => __('wall-insulation.optional.facade-surface.title'),
        'insulation_wall_surface' => __('wall-insulation.optional.insulated-surface.title'),

        // glass insulation
        'building_paintwork_statuses.last_painted_year' => __('insulated-glazing.paint-work.last-paintjob.title'),

        // floor insulation
        'building_features.floor_surface' => __('floor-insulation.surface.title'),
        'building_features.insulation_surface' => __('floor-insulation.insulation-surface.title'),

        // solar panels
        'building_pv_panels.number' => __('solar-panels.number.title'),
        'building_pv_panels.peak_power' => __('solar-panels.peak-power.title')
    ],
];
