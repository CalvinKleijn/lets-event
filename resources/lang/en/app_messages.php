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
            'success' => 'You\'ve successfully updated your profile.'
        ]
    ],
    'tournament' => [
        'error_start_date_in_past' => 'The filled in start date is in the past.',
        'not_authorized' => 'You may not edit this tournament because you are not an admin.',
        'join' => [
          'error_already_participant' => 'You are already participating in this tournament.',
        ],
        'leave' => [
            'success' => 'You have successfully left the tournament.',
            'already_started' => 'You can\'t leave the tournament because it has already started.'
        ],
        'store' => [
            'success' => 'You\'ve successfully created a tournament.',
        ],
//        'edit' => [
//        ],
        'update' => [
            'success' => 'You\'ve successfully updated the tournament.',
        ],
        'destroy' => [
            'success' => 'You have successfully deleted the tournament',
            'error_on_destroy' => 'Something went wrong wilst attempting to delete the tournament.',
            'tournament_not_available' => 'The tournament is not available anymore.'
        ],
        'request_referee' => [
            'already_requested' => 'You\'ve already requested to be a referee.'
        ]
    ],
    'tournament_admin' => [
        'delete_user' => [
            'success' => 'You have successfully deleted a user.',
            'error_on_delete' => 'Something went wrong whilst trying to delete the admin.',
            'minimum_admin_count_error' => 'The admin could not be deleted as there must always be atleast one admin.'
        ],
        'store_user' => [
            'success' => 'You have successfully added the role to the user.',
            'already_has_role' => 'The specified user already has this role.',
            'add_role_error' => 'The specified role could not be added to the user.'
        ],
        'add_referee' => [
            'success' => 'The user is now a referee.',
            'already_a_referee' => 'This user is a referee already.'
        ],
        'deny_referee' => [
            'request_denied' => 'Your request to become a referee has been denied.',
            'already_denied' => 'This user has already been denied.'
        ]
    ],

];
