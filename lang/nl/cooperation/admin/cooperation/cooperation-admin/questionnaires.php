<?php

return [
    'index' => [
        'header' => 'Alle vragenlijsten voor uw coöperatie',
        'table' => [
            'columns' => [
                'questionnaire-name' => 'Vragenlijst naam',
                'step' => 'Komt na stap',
                'active' => 'Actief',
                'actions' => 'Acties',
                'see-results' => 'Bekijk resultaten',
                'edit' => 'Bewerk vragenlijst',
                'destroy' => 'Verwijder vragenlijst',
            ],
        ],
    ],
    'create' => [
        'header' => 'Vragenlijst aanmaken',
        'leave-creation-tool' => 'Keer terug naar overzicht',
    ],
    'edit' => [
        'header' => 'Vragenlijst bewerken',
        'types' => [
            'text' => 'Kort antwoord',
            'textarea' => 'Alinea',
            'select' => 'Dropdownmenu',
            'radio' => 'Selectievakjes',
            'checkbox' => 'Meerkeuze',
            'date' => 'Datum',
        ],
        'tabs' => [
            'edit-questionnaire' => 'Vragenlijst info',
            'edit-questions' => 'Vragen van de vragenlijst',
        ],
        'add-validation' => 'Voeg validatie toe',
        'remove-validation' => 'Verwijder validatie',
        'success' => 'Vragenlijst is bijgewerkt',
        'leave-warning' => 'Let op! Alle wijzigingen zullen verloren gaan. Uw hiervoor gemaakte formulier is dan niet meer terug te krijgen!',
    ],
    'destroy' => [
        'are-you-sure' => 'Dit verwijderd de vragenlijst, vragen en de gegeven antwoorden. Weet u zeker dat u wilt doorgaan?',
        'success' => 'Vragenlijst verwijderd',
    ],

    'form' => [
        'name' => [
            'label' => 'Naam',
            'placeholder' => 'Nieuwe vragenlijst',
        ],
        'step' => [
            'label' => 'Na stap',
            'placeholder' => 'Selecteer een stap',
        ],
        'required' => [
            'label' => 'Verplicht',
        ],
        'question' => [
            'label' => 'Vraag',
        ],
        'option' => [
            'label' => 'Opties',
            'placeholder' => 'Optie...',
        ],
    ],
    'form-builder' => [
        'rules' => [
            'numeric' => 'Getal',
            'string' => 'Tekst',
        ],
        'optional-rules' => [
            'numeric' => [
                'between' => 'Tussen',
                'min' => 'Minimale grootte',
            ],
            'string' => [
                'email' => 'E-mailadres',
                'max' => 'Maximaal aantal letters',
            ],
        ],
        'extra-fields' => [
            'min' => [
                'placeholder' => 'Min...',
            ],
            'max' => [
                'placeholder' => 'Max...',
            ],
        ],
    ],
];
