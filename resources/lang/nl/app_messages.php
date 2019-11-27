<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Error messages Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used by the paginator library to build
    | the simple pagination links. You are free to change them to anything
    | you want to customize your views to better match your application.
    |
    */
    'user' => [
        'update' => [
            'success' => 'Je hebt je profiel met success geüpdatet.'
        ]
    ],
    'tournament' => [
        'error_start_date_in_past' => 'De ingevulde start datum ligt in het verleden.',
        'not_authorized' => 'Je mag dit toernooi niet bewerken aangezien je geen beheerder bent.',
        'join' => [
            'error_already_participant' => 'Je doet al mee aan dit toernooi.',
        ],
        'leave' => [
            'success' => 'Je hebt het toernooi met success verlaten.',
            'already_started' => 'Je kan het toernooi niet verlaten omdat het al gestart is.'
        ],
        'store' => [
            'success' => 'Je hebt met success een toernooi aangemaakt.',
        ],
//        'edit' => [
//        ],
        'update' => [
            'success' => 'Je hebt met success het toernooi geüpdatet',
        ],
        'destroy' => [
            'success' => 'Je hebt met success het toernooi verwijderd.',
            'error_on_destroy' => 'Er is iets fout gegaan bij het verwijderen van het toernooi.',
            'tournament_not_available' => 'Het toernooi is niet langer beschikbaar meer.'
        ],
        'request_referee' => [
            'already_requested' => 'Je hebt al een verzoek tot scheidsrechter gedaan.'
        ]
    ],
    'tournament_admin' => [
        'delete_user' => [
            'success' => 'Je hebt met success een gebruiker verwijderd van het toernooi.',
            'error_on_delete' => 'Er is iets fout gegaan bij het verwijderen van een gebruiker van een toernooi.',
            'minimum_admin_count_error' => 'De beheerder kon niet verwijderd worden omdat er altijd minimaal een beheerder moet zijn.'
        ],
        'store_user' => [
            'success' => 'Je hebt met success een rol toegevoegd aan de gebruiker.',
            'already_has_role' => 'De gebruiker heeft deze rol al.',
            'add_role_error' => 'De opgegeven rol kon niet aan de gebruiker worden toegevoegd.'
        ],
        'add_referee' => [
            'success' => 'De gebruiker is nu een scheidsrechter.',
            'already_a_referee' => 'De gebruiker is al een scheidsrechter.'
        ],
        'deny_referee' => [
            'request_denied' => 'Je aanvraag om een scheidsrechter te worden is afgewezen.',
            'already_denied' => 'Deze gebruiker is al eens afgewezen.'
        ]
    ],

];
