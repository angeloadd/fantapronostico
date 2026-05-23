<?php

declare(strict_types=1);

$appName = ucfirst(config('app.name'));

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'failed' => 'Non abbiamo trovato un account con queste credenziali.',
    'password' => 'Non abbiamo trovato un account con queste credenziali.',
    'throttle' => 'Troppi tentativi di login. Ti preghiamo di riprovare tra :seconds secondi.',

    'login' => [
        'title' => 'Accedi - '.$appName,
        'nav' => 'Accedi',
        'btn' => 'Accedi',
        'request_password_reset' => 'Password dimenticata?',
    ],
    'register' => [
        'title' => 'Iscriviti - '.$appName,
        'nav' => 'Iscriviti',
        'btn' => 'Crea nuovo account',
    ],
    'verify_email' => [
        'title' => 'Verifica Email - FP2024',
        'salutation' => 'Benvenuto :username',
        'heading' => 'Verifica la tua email!',
        'paragraph' => 'Per poter partecipare al gioco devi verificare la tua email. Abbiamo inviato un link alla email che hai fornito durante la registrazione: :email.',
        'paragraph2' => 'Se non hai ricevuto niente clicca sul link qui sotto.',
        'btn' => 'Invia link di verifica',
    ],
    'league' => [
        'title' => 'Leghe',
    ],
    'request_password_reset' => [
        'title' => 'Password Dimenticata - '.$appName,
        'paragraph' => 'Inserisci la tua e-mail e ti invieremo un link per reimpostare la tua password.',
        'btn' => 'Invia Link',
    ],
    'reset_password' => [
        'title' => 'Reimposta Password - '.$appName,
        'btn' => 'Reimposta Password',
    ],
];
