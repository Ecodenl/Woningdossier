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
        'numeric' => 'De :attribute moet tussen :min en :max liggen.',
        'file'    => 'De :attribute moet tussen :min en :max kilobytes groot zijn.',
        'string'  => 'De :attribute moet tussen :min en :max tekens zijn.',
        'array'   => 'De :attribute moet tussen :min en :max items bevatten.',
    ],
    'boolean'              => 'De :attribute moet waar of onwaar zijn.',
    'confirmed'            => 'De :attribute bevestiging is niet correct.',
    'current_password'     => 'Uw huidige password is niet correct.',
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
    'filled'               => 'Het :attribute veld moet ingevuld zijn.',
    'image'                => 'De :attribute moet een image zijn.',
    'in'                   => 'De geselecteerde :attribute is ongeldig.',
    'in_array'             => 'Het :attribute veld is geen optie in :other.',
    'integer'              => 'De :attribute moet een afgerond getal zijn.',
    'ip'                   => 'De :attribute moet een geldig IP adres zijn.',
    'ipv4'                 => 'De :attribute moet een geldig IPv4 adres zijn.',
    'ipv6'                 => 'De :attribute moet een geldig IPv6 adres zijn.',
    'json'                 => 'De :attribute moet een geldige JSON string zijn.',
    'max'                  => [
        'numeric' => 'De :attribute mag niet groter dan :max zijn.',
        'file'    => 'De :attribute mag niet groter dan :max kilobytes zijn.',
        'string'  => 'De :attribute mag niet meer dan :max karakters bevatten.',
        'array'   => 'De :attribute mag niet meer dan :max items bevatten.',
    ],
    'mimes'                => 'De :attribute moet een bestand zijn met type: :values.',
    'mimetypes'            => 'De :attribute moet een bestand zijn met type: :values.',
    'min'                  => [
        'numeric' => 'De :attribute moet tenminste :min zijn.',
        'file'    => 'De :attribute moet tenminste :min kilobytes groot zijn.',
        'string'  => 'De :attribute moet tenminste :min karakters bevatten.',
        'array'   => 'De :attribute moet tenminste :min items bevatten.',
    ],
    'not_in'               => 'De geselecteerde :attribute is ongeldig.',
    'numeric'              => 'Het veld moet een getal bevatten',
    'present'              => 'De :attribute moet gevuld zijn.',
    'phone_number'         => 'Het veld Telefoonnummer bevat geen geldig telefoonnummer.',
    'postal_code'          => 'De opgegeven postcode is ongeldig.',
    'house_number'         => 'Het opgegeven huisnummer is ongeldig.',
    'regex'                => 'Het :attribute formaat is ongeldig.',
    'required'             => 'Het :attribute veld is verplicht.',
    'required_if'          => 'Het :attribute veld is verplicht wanneer :other :value is.',
    'required_unless'      => 'Het :attribute veld is verplicht tenzij :other in :values zit.',
    'required_with'        => 'Het :attribute veld is verplicht wanneer :values aanwezig is.',
    'required_with_all'    => 'Het :attribute veld is verplicht wanneer :values aanwezig is.',
    'required_without'     => 'Het :attribute veld is verplicht wanneer :values niet aanwezig is.',
    'required_without_all' => 'Het :attribute veld is verplicht wanneer geen van :values aanwezig is.',
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
    'uploaded'             => 'Het is mislukt om :attribute failed to upload.',
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
        'alpha_space' => 'Het veld :attribute mag alleen letters en spaties bevatten.',
        'needs_to_be_lower_or_same_as' => 'Dit veld moet gelijk of kleiner zijn dan het veld :otherfield',
        'surface' => 'Dit veld is verplicht als u een dak type heeft gekozen.',
        'is-user-member-of-cooperation' => 'De opgegeven gebruiker is geen lid van de huidige cooperatie',
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
        'needs-to-be-filled' => 'Dit veld moet gevuld zijn',

        'building_paintwork_statuses' => [
            'last_painted_year' => [
                'required' => 'Wanneer is uw schilderwerk voor het laatst gedaan?',
                'between' => 'Wanneer het schilderwerk voor het laatst gedaan is moet een geldig jaartal bevatten',
            ],
        ],
    ],

    'needs_to_be_lower_or_same_as' => 'Dit veld moet gelijk of kleiner zijn dan het veld :otherfield',

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

    'attributes' => [],
];
