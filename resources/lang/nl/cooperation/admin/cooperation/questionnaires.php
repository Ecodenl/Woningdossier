<?php

return [
    'index' => [
        'header' => 'Alle vragenlijsten voor uw coöperatie',
        'table' => [
            'columns' => [
                'questionnaire-name' => 'Vragenlijst naam',
                'step' => 'Komt na stap',
                'active' => 'Actief',
                'active-on' => 'Actief',
                'active-off' => 'Niet actief',
                'actions' => 'Acties',
                'edit' => 'Bewerk vragenlijst',
                'destroy' => 'Verwijder vragenlijst',
            ],
        ],
    ],
    'create' => [
        'submit' => 'Vragenlijst aanmaken',
        'success' => 'Vragenlijst is toegevoegd',
    ],
    'edit' => [
        'add-validation' => 'Voeg validatie toe',
        'success' => 'Vragenlijst is bijgewerkt',
    ],
    'destroy' => [
        'are-you-sure' => 'Dit verwijderd de vragenlijst, vragen en de gegeven antwoorden. Weet u zeker dat u wilt doorgaan?',
//        'success' => 'Vragenlijst verwijderd',
    ],
    'shared' => [
        'column-translations' => [
            'name' => [
                'label' => 'Naam:',
                'placeholder' => 'Nieuwe vragenlijst',
            ],
            'after-step' => [
                'label' => 'Na stap:',
            ],
        ],
        'types' => [
            'text' => [
                'label' => 'Kort antwoord',
            ],
            'textarea' => [
                'label' => 'Alinea',
                'placeholder' => 'Stel uw vraag waar een langer antwoord voor nodig is...',
            ],
            'select' => [
                'label' => 'Dropdownmenu',
            ],
            'radio' => [
                'label' => 'Selectievakjes',
            ],
            'date' => [
                'label' => 'Datum',
            ],
            'checkbox' => [
                'label' => 'Meerkeuze',
            ],

            'default-label' => 'Vraag',
            'default-placeholder' => 'Vraag',
            'default-option-label' => 'Optie',
            'default-option-placeholder' => 'Optie toevoegen',
            'add' => '(toevoegen)',
        ],

        'leave-creation-tool' => 'Keer terug naar overzicht',
        'leave-creation-tool-warning' => 'Let op!, alle wijzigingen zullen verloren gaan. Uw hiervoor gemaakte formulier is dan niet meer terug te krijgen!',
    ],
];