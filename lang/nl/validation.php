<?php

use App\Helpers\MediaHelper;

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
        'numeric' => ':Attribute moet tussen :min en :max liggen.',
        'file'    => 'De :attribute moet tussen :min en :max kilobytes groot zijn.',
        'string'  => 'De :attribute moet tussen :min en :max tekens zijn.',
        'array'   => 'De :attribute moet tussen :min en :max items bevatten.',
    ],
    'boolean'              => 'De :attribute moet waar of onwaar zijn.',
    'confirmed'            => 'De :attribute bevestiging is niet correct.',
    'hash_check'           => ':Attribute is niet correct.',
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
    'filled'               => ':Attribute veld moet ingevuld zijn.',
    'gt'                   => [
        'numeric' => 'De :attribute moet groter zijn dan :value.',
        'file'    => 'De :attribute moet groter zijn dan :value kilobytes.',
        'string'  => 'De :attribute moet meer dan :value tekens bevatten.',
        'array'   => 'De :attribute moet meer dan :value waardes bevatten.',
    ],
    'gte'                  => [
        'numeric' => 'De :attribute moet groter of gelijk zijn aan :value.',
        'file'    => 'De :attribute moet groter of gelijk zijn aan :value kilobytes.',
        'string'  => 'De :attribute moet minimaal :value tekens bevatten.',
        'array'   => 'De :attribute moet :value waardes of meer bevatten.',
    ],
    'image'                => 'De :attribute moet een image zijn.',
    'in'                   => 'De geselecteerde :attribute is ongeldig.',
    'in_array'             => ':Attribute veld is geen optie in :other.',
    'integer'              => 'De :attribute moet een afgerond getal zijn.',
    'ip'                   => 'De :attribute moet een geldig IP adres zijn.',
    'ipv4'                 => 'De :attribute moet een geldig IPv4 adres zijn.',
    'ipv6'                 => 'De :attribute moet een geldig IPv6 adres zijn.',
    'json'                 => 'De :attribute moet een geldige JSON string zijn.',
    'max'                  => [
        'numeric' => ':Attribute mag niet groter dan :max zijn.',
        'file'    => 'De :attribute mag niet groter dan :max kilobytes zijn.',
        'string'  => 'De :attribute mag niet meer dan :max karakters bevatten.',
        'array'   => 'De :attribute mag niet meer dan :max items bevatten.',
    ],
    'mimes'                => 'De :attribute moet een bestand zijn met type: :values.',
    'mimetypes'            => 'De :attribute moet een bestand zijn met type: :values.',
    'min'                  => [
        'numeric' => ':Attribute moet tenminste :min zijn.',
        'file'    => ':Attribute moet tenminste :min kilobytes groot zijn.',
        'string'  => 'De :attribute moet tenminste :min karakters bevatten.',
        'array'   => 'De :attribute moet tenminste :min items bevatten.',
    ],
    'lt'                   => [
        'numeric' => 'De :attribute moet kleiner zijn dan :value.',
        'file'    => 'De :attribute moet kleiner zijn dan :value kilobytes.',
        'string'  => 'De :attribute moet minder dan :value tekens bevatten.',
        'array'   => 'De :attribute moet minder dan :value waardes bevatten.',
    ],
    'lte'                  => [
        'numeric' => 'De :attribute moet kleiner of gelijk zijn aan :value.',
        'file'    => 'De :attribute moet kleiner of gelijk zijn aan :value kilobytes.',
        'string'  => 'De :attribute moet maximaal :value tekens bevatten.',
        'array'   => 'De :attribute moet :value waardes of minder bevatten.',
    ],
    'multiple_of'          => ':Attribute moet een veelvoud van :value zijn.',
    'not_in'               => 'De waarde van :attribute is ongeldig.',
    'not_regex'            => 'De :attribute formaat is ongeldig.',
    'numeric'              => ':Attribute moet een getal bevatten',
    'present'              => 'De :attribute moet gevuld zijn.',
    'phone_number'         => ':Attribute is een ongeldig telefoonnummer.',
    'postal_code'          => ':Attribute is een ongeldige postcode.',
    'house_number'         => ':Attribute is een ongeldig huisnummer.',
    'house_number_extension'    => ':Attribute is een ongeldige huisnummer toevoeging.',
    'regex'                => ':Attribute formaat is ongeldig.',
    'required'             => ':Attribute veld is verplicht.',
    'required_if'          => ':Attribute veld is verplicht wanneer :other :value is.',
    'required_unless'      => ':Attribute veld is verplicht tenzij :other in :values zit.',
    'required_with'        => ':Attribute veld is verplicht wanneer :values aanwezig is.',
    'required_with_all'    => ':Attribute veld is verplicht wanneer :values aanwezig is.',
    'required_without'     => ':Attribute veld is verplicht wanneer :values niet aanwezig is.',
    'required_without_all' => ':Attribute veld is verplicht wanneer geen van :values aanwezig is.',
    'same'                 => 'De :attribute en :other moeten overeenkomen.',
    'size'                 => [
        'numeric' => 'De :attribute moet :size zijn.',
        'file'    => 'De :attribute moet :size kilobytes zijn.',
        'string'  => 'De :attribute moet :size karakters zijn.',
        'array'   => 'De :attribute moet :size items bevatten.',
    ],
    'starts_with'          => ':Attribute moet starten met een van de volgende: :values.',
    'string'               => 'De :attribute moet een tekst zijn.',
    'timezone'             => 'De :attribute moet een geldig tijdzone zijn.',
    'unique'               => 'De :attribute is al geregistreerd.',
    'uploaded'             => 'Het is mislukt om :attribute te uploaden.',
    'url'                  => 'De :attribute formaat is ongeldig.',
    'uuid'                 => ':Attribute moet een geldig UUID zijn.',

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
        'alpha_space' => ':Attribute mag alleen letters en spaties bevatten.',
        'surface' => 'Dit veld is verplicht als u een dak type heeft gekozen.',
        'is-user-member-of-cooperation' => 'De opgegeven gebruiker is geen lid van de huidige cooperatie',
        'needs-to-be-filled' => 'Dit veld moet gevuld zijn',

        'building_roof_types.flat.insulation_roof_surface' => [
            'needs_to_be_lower_or_same_as' => '"Te isoleren oppervlakte van het plate dak" moet gelijk of kleiner zijn dan het veld "Dakoppervlak platte dak"',
        ],
        'recovery_code' => 'De opgegeven herstelcode is ongeldig.',
        'code' => 'De opgegeven 2FA code is ongeldig',

        'building_roof_types.pitched.insulation_roof_surface' => [
            'needs_to_be_lower_or_same_as' => '"Te isoleren oppervlakte van het hellende dak" moet gelijk of kleiner zijn dan het veld "Dakoppervlak hellend dak"',
        ],

        'building_services.7.extra.value' => [
            'required' => 'Het aantal zonnepanelen is verplicht',
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
        ],
        'accounts.email' => [
            'unique' => 'Het e-mailadres is al geregistreerd.',
        ],
        'email' => [
            'unique' => 'Het e-mailadres is al geregistreerd.',
        ],

        'password' => [
            'min' => 'Het wachtwoord moet minmaal ' . Hoomdossier::PASSWORD_LENGTH . ' karakters bevatten.',
            'confirmed' => 'Wachtwoord bevestiging komt niet overheen.',
        ],
        'account.password' => [
            'min' => 'Het wachtwoord moet minmaal ' . Hoomdossier::PASSWORD_LENGTH . ' karakters bevatten.',
            'confirmed' => 'Wachtwoord bevestiging komt niet overheen.',
        ],

        'cooperation_settings.register_url' => [
            'url' => 'Het formaat is ongeldig, de url moet beginnen met https:// of http://.'
        ],

        'contact-id' => [
            'not-found' => 'Opgegeven contact ID voor :attribute is niet bekend als gebruiker in het Hoomdossier.'
        ],

        'users' => [
            'incorrect-role' => 'Gevonden gebruiker voor :attribute heeft niet de verwachte rol (:role).',
        ],

        'building-coach-statuses' => [
            'no-access' => 'Bewoner heeft geen toestemming gegeven om gegevens te delen.',
            'already-linked' => 'De opgegeven coach is al gekoppeld aan de gegeven woning.',
        ],
        'uploader' => [
            'wrong-files' => 'Een of meerdere bestanden zijn ongeldig of te groot.',
            'max-size' => 'Maximale toegestane bestandsgrootte is :size MB',
        ],
    ],
    'custom-rules' => [
        'language-required' => 'Er moet op het minst 1 vertaling zijn in :locale voor :attribute.',
        'max-filename-length' => 'De bestandsnaam van :attribute mag niet langer zijn dan :length karakters.',
        'rule-unique' => ':Attribute bestaat al.',

        'municipalities' => [
            'already-coupled' => ':Attribute is al gekoppeld aan een gemeente!',
        ],

        'api' => [
            'incorrect-vbjehuis-value' => ':Attribute is geen geldige VerbeterJeHuis waarde!',
        ],
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
    'admin' => [
        'example-buildings' => [
            'new' => [
                'build_year' => 'Bouwjaar voor nieuwe content kan niet leeg zijn',
            ],
            'existing' => [
                'build_year' => 'Bouwjaar kan niet leeg zijn',
            ],
        ],
    ],

    // note, when a key needs a wildcard add it to the custom array.
    // A wildcard wont work in the attributes array but will in the custom array, needs more work but at least we dont need to hardcode the * numbers.
    'attributes' => [
        'nl' => 'Nederlands',
        'en' => 'Engels',

        'document' => 'geüpload bestand',
        'documents' => 'bestanden',
        'documents.*' => 'een bestand',

        'building_services.7.extra.value' => 'Het aantal zonnepanelen',
        'email' => 'E-mailadres',
        'password' => 'Wachtwoord',

        // Register fields
        'number' => 'Huisnummer',
        'postal_code' => 'Postcode',
        'first_name' => 'Voornaam',
        'last_name' => 'Achternaam',
        'street' => 'Straat',
        'city' => 'Plaats',
        'phone_number' => 'Telefoonnummer',
        'house_number' => 'Huisnummer',
        'house_number_extension' => 'Toevoeging',
        'extra.contact_id' => 'contact ID',

        // New measure
        'custom_measure_application.name' => 'onderwerp',
        'custom_measure_application.measure_category' => 'maatregelcategorie',
        'custom_measure_application.info' => 'beschrijving',
        'custom_measure_application.hide_costs' => 'geen kosten',
        'custom_measure_application.costs.from' => 'investering vanaf',
        'custom_measure_application.costs.to' => 'investering tot',
        'custom_measure_application.savings_money' => 'besparing',

        'account.current_password' => 'huidig wachtwoord',
        'account.password' => 'wachtwoord',
        'account.password_confirmation' => 'wachtwoord bevestigen',

        // Measure applications
        'measure_applications.measure_name' => 'naam',
        'measure_applications.measure_name.*' => 'naam',
        'measure_applications.measure_info' => 'info',
        'measure_applications.measure_info.*' => 'info',
        'measure_applications.cost_range.from' => 'investering vanaf',
        'measure_applications.cost_range.to' => 'investering tot',
        'measure_applications.savings_money' => 'besparing',
        'measure_applications.configurations.icon' => 'icoon',

        // general data
        // TODO: Check if these are still relevant
        'building_features.building_layers' => 'hoeveel bouwlagen heeft het huis?',
        'building_features.surface' => 'wat is de gebruiksoppervlakte van de woning?',
        'building_features.build_year' => 'wat is het bouwjaar?',
        'build_year' => 'wat is het bouwjaar?',
        'user_energy_habits.resident_count' => 'Wat is het aantal bewoners?',
        'user_energy_habits.thermostat_high' => 'op welke temperatuur staat de thermostaat op de hoge stand?',
        'user_energy_habits.thermostat_low' => 'op welke temperatuur staat de thermostaat op lage stand?',
        'user_energy_habits.amount_electricity' => 'wat is het elektragebruik van het afgelopen jaar?',
        'user_energy_habits.amount_gas' => 'wat is het gasgebruik van afgelopen jaar?',

        'building_services.boiler.extra' => 'wanneer is de huidige CV ketel geplaatst?',
        'service.6.extra.year' => 'jaartal',

        'services.total-sun-panels.extra.year' => 'indien aanwezig, wanneer geplaatst',
        'services.total-sun-panels.extra.value' => 'hoeveel zonnepanelen zijn er aanwezig?',
        'building_pv_panels.total_installed_power' => 'geinstalleerd vermogen (totaal)',

        'building_features.window_surface' => 'totale raamopperlake van de woning',

        // ventilation
        'building_ventilations.how' => 'hoe wordt de woning nu geventileerd?',

        // roof insulation
        'building_roof_types.pitched.roof_surface' => 'dakoppervlak hellend dak',
        'building_roof_types.pitched.insulation_roof_surface' => 'te isoleren oppervlakte van het hellende dak',

        'building_roof_types.flat.roof_surface' => 'dakoppervlak van platte dak',
        'building_roof_types.flat.insulation_roof_surface' => 'te isoleren oppervlakte van het platte dak',

        'building_roof_types.pitched.extra.zinc_replaced_date' => 'wanneer is het zinkwerk voor het laatst vernieuwd?',

        'building_roof_types.flat.extra.zinc_replaced_date' => 'wanneer is het zinkwerk voor het laatst vernieuwd?',
        'building_roof_types.flat.extra.bitumen_replaced_date' => 'wanneer is het bitumen dak voor het laatst vernieuwd?',

        // wall insulation
        'building_features.cavity_wall' => 'heeft deze woning een spouwmuur?',
        'building_features.facade_plastered_painted' => 'is de gevel gestuct of geverfd ?',
        'building_features.wall_surface' => 'geveloppervlakte van de woning',
        'building_features.insulation_wall_surface' => 'te isoleren oppervlakte',

        // glass insulation
        'building_paintwork_statuses.last_painted_year' => 'wanneer is het schilderwerk voor het laatst gedaan? (jaargetal)',

        // floor insulation
        'building_features.floor_surface' => 'vloeroppervlak van de woning',
        'building_features.insulation_surface' => 'te isoleren oppervlakte',

        // solar panels
        'building_pv_panels.number' => 'hoeveel zonnepanelen moeten er komen?',
        'building_pv_panels.peak_power' => 'piekvermogen per paneel',

        // Cooperation measure applications
        'cooperation_measure_applications.name' => 'Naam',
        'cooperation_measure_applications.name.*' => 'Naam',
        'cooperation_measure_applications.measure_category' => 'maatregelcategorie',
        'cooperation_measure_applications.info' => 'Info',
        'cooperation_measure_applications.info.*' => 'Info',
        'cooperation_measure_applications.costs.from' => 'Investering vanaf',
        'cooperation_measure_applications.costs.to' => 'Investering tot',
        'cooperation_measure_applications.savings_money' => 'Besparing',
        'cooperation_measure_applications.extra.icon' => 'Icoon',

        // Cooperation settings
        'cooperation_settings.' . \App\Helpers\Models\CooperationSettingHelper::SHORT_REGISTER_URL => 'register URL',

        // User data
        'accounts.email' => 'e-mailadres',
        'users.first_name' => 'voornaam',
        'users.last_name' => 'achternaam',
        'users.phone_number' => 'telefoonnummer',
        'users.extra.contact_id' => 'contact ID',
        'buildings.postal_code' => 'postcode',
        'buildings.number' => 'huisnummer',
        'buildings.extension' => 'toevoeging',
        'buildings.street' => 'straat',
        'buildings.city' => 'stad',

        // Address
        'address.postal_code' => 'postcode',
        'address.number' => 'huisnummer',
        'address.extension' => 'toevoeging',
        'address.street' => 'straat',
        'address.city' => 'stad',

        // API
        'building_coach_statuses.coach_contact_id' => 'coach contact ID',
        'building_coach_statuses.resident_contact_id' => 'bewoner contact ID',

        // Questionnaires
        'questionnaires.steps' => 'na stap',
        'questionnaires.steps.*' => 'na stap',
        'questionnaires.name.*' => 'naam',

        'cooperations.name' => 'naam van de coöperatie',
        'cooperations.slug' => 'slug',
        'cooperations.cooperation_email' => 'contact e-mailadres',
        'cooperations.website_url' => 'website URL',
        'cooperations.econobis_wildcard' => 'Econobis Domein Wildcard',
        'cooperation.econobis_api_key' => 'Econobis API key',

        // Cooperation presets
        // Cooperation measure applications
        'content.name' => 'naam',
        'content.name.*' => 'naam',
        'content.info' => 'info',
        'content.info.*' => 'info',
        'content.relations.mapping.measure_category' => 'maatregelcategorie',
        'content.costs.from' => 'investering vanaf',
        'content.costs.to' => 'investering tot',
        'content.savings_money' => 'besparing',
        'content.extra.icon' => 'icoon',
        'content.is_extensive_measure' => 'is grote maatregel',

        // Municipalities
        'municipalities.name' => 'naam',
        'bag_municipalities' => 'BAG gemeente(n)',
        'bag_municipalities.*' => 'BAG gemeente(n)',
        'vbjehuis_municipality' => 'VerbeterJeHuis gemeente',

        // Measure caategories
        'measure_categories.name' => 'naam',
        'vbjehuis_measure' => 'VerbeterJeHuis maatregel',

        // Woonplan comments
        'residentCommentText' => 'opmerkingen bewoner',
        'coachCommentText' => 'opmerkingen coach',

        // Media
        'medias.' . MediaHelper::LOGO => "logo",
        'medias.' . MediaHelper::BACKGROUND => "achtergrond",
        'medias.' . MediaHelper::PDF_BACKGROUND => "pdf achtergrond",
        'medias.' . MediaHelper::GENERIC_FILE => "generiek bestand",
        'medias.' . MediaHelper::GENERIC_IMAGE => "generieke foto",
        'medias.' . MediaHelper::REPORT => "rapportage",
        'medias.' . MediaHelper::QUOTATION => "offerte",
        'medias.' . MediaHelper::INVOICE => "factuur",
        'medias.' . MediaHelper::BILL => "rekening",

        // Admin
        'building.note' => 'opmerking over de woning',

        // Clients
        'clients.name' => 'naam',

        // Questionnaires
        'questions.*.name' => 'naam',
        'questions.*.options.*.name' => 'optie',
    ],

    'values' => [
        'defaults' => [
            'yes' => 'Ja',
        ],
    ],
];
