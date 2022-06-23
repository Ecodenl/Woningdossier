<?php

return [
    'show' => [
        'user-disallowed-access' => 'Deze woning kan niet worden bewerkt of worden bekeken, de gebruiker heeft het toestemmings vinkje uit staan in zijn instellingen',
        'give-role' => 'Weet u zeker dat u deze rol aan deze gebruiker wilt toevoegen?',
        'remove-role' => 'Weet u zeker dat u deze rol van deze gebruiker wilt verwijderen?',

        'save-building-detail' => 'Opmerking opslaan',

        'edit' => [
            'label' => 'Gegevens bewerken',
            'button' => '<i class="glyphicon glyphicon-pencil"></i>',
        ],
        'fill-for-user' => [
            'label' => 'Woning als coach bewerken',
            'button' => '<i class="glyphicon glyphicon-edit"></i>',
        ],

        'header' => 'Detail overzicht :name, :street-and-number, :zipcode-and-city, :email, :phone-number',

        'observe-building' => [
            'label' => 'Woning bekijken',
            'button' => '<i class="glyphicon glyphicon-eye-open"></i>',
        ],
        'delete-account' => [
            'label' => 'Account verwijderen',
            'button' => '<i class="glyphicon glyphicon-trash"></i>',
        ],
        'role' => [
            'label' => 'Rol',
            'button' => 'Bijwerken',
        ],
        'status' => [
            'current' => 'Huidige status: ',
            'label' => 'Status: ',
            'button' => 'Kies status',
        ],
        'associated-coach' => [
            'label' => 'Gekoppelde coaches',
            'button' => 'Kies coach',
        ],
        'appointment-date' => [
            'label' => 'Datum afspraak',
            'button' => 'Kies datum',
        ],

        'has-building-access' => [
            'no' => 'Geen toegang tot woning',
            'yes' => 'Toegang tot woning',
        ],

        'delete-user' => 'Weet u zeker dat u deze gebruiker wilt verwijderen, deze actie kan niet ongedaan worden gemaakt',
        'revoke-access' => 'Weet u zeker dat u deze gebruiker van de van groeps-chat wilt verwijderen, de gebruiker heeft hierna geen toegang meer tot de woning.',
        'add-with-building-access' => 'Weet u zeker dat u deze gebruiker aan de groeps-chat toegang wilt geven ? De gebruiker heeft hierna ook toegang tot de woning',

        'set-status' => 'Weet u zeker dat u deze status wilt zetten voor het huidige gebouw?',
        'set-appointment-date' => 'Weet u zeker dat u deze datum wilt zetten voor het huidige gebouw?',
        'set-empty-appointment-date' => 'Weet u zeker dat u de afspraak wilt verwijderen?',

        'tabs' => [
            'messages-public' => [
                'user-notification' => [
                    'yes' => 'Gebruiker heeft notificaties aanstaan, hij zal op de hoogte worden gesteld van de verstuurde berichten.',
                    'no' => 'Gebruiker ontvangt hier geen melding van.',
                ],
                'title' => 'Berichten bewoner',
            ],
            'messages-intern' => [
                'title' => 'Berichten intern',
            ],
            'comments-on-building' => [
                'title' => 'Opmerkingen bij woning',
                'note' => 'Opmerking over de woning.',
                'save' => 'Opmerking opslaan',
            ],
            'fill-in-history' => [
                'title' => 'Invulhistorie',
                'table' => [
                    'columns' => [
                        'user' => 'Gebruiker die de actie heeft gedaan',
                        'building' => 'Actie voor woning',
                        'for-user' => 'Actie op gebruiker',
                        'message' => 'Bericht',
                        'happened-on' => 'Gebeurt op',
                    ],
                ],
            ],
        ],
        'next' => 'Volgende',
        'previous' => 'Vorige',
    ],
    'edit' => [
        'account-user-info-title' => 'Gebruiker gegevens',
        'address-info-title' => 'Adres gegevens',
        'form' => [
            'submit' => 'Gegevens bijwerken',
        ],
    ],
    'update' => [
        'success' => 'Gegevens bijgewerkt',
    ],
];
