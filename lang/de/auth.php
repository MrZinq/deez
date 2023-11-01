<?php

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

    'login' => 'Anmelden',
    'login_facebook' => 'Mit Facebook anmelden',
    'login_twitter' => 'Mit Twitter anmelden',
    'login_google' => 'Mit Google anmelden',
    'login_or' => 'oder',
    'username' => 'Benutzername',
    'email' => 'E-mail',
    'password' => 'Passwort',
    'password_confirmation' => 'Passwort bestätigen',
    'password_forgot' => 'Passwort vergessen?',
    'remember_me' => 'Angemeldet bleiben',
    'no_account_question' => "Noch kein Benutzerkonto?",
    'no_account_question_create' => 'Registriere dich kostenlos!',
    'create_account' => 'Benutzerkonto anlegen',
    'failed' => 'Anmeldung fehlgeschlagen.',
    'throttle' => 'Zu viele Anmeldeversuche. Bitte versuche es in :seconds Sekunden nochmal.',
    'unknown' => 'Ein unbekannter Fehler ist aufgetreten',

    'welcome_back' => 'Willkommen zurück, :User_name!',
    'see_you' => 'Bis bald!',
    'deactivated' => 'Dein Konto wurde deaktiviert!',

    'confirmation' => [
        'already_confirmed' => 'Dein Konto ist bereits bestätigt.',
        'confirm' => 'Bestätige dein Konto!',
        'created_confirm' => 'Dein Konto wurde erfolgreich erstellt. Wir haben dir eine E-mail gesendet um dein Konto zu bestätigen.',
        'mismatch' => 'Bestätigungscode stimmt nicht überein.',
        'not_found' => 'Bestätigungscode existiert nicht.',
        'resend' => 'Dein Konto wurde noch nicht bestätigt. Bitte klicke auf den Bestätigungslink in deiner E-mail, oder <a href="' . route('frontend.auth.account.confirm.resend', ':user_id') . '">klicke hier/a> um die Bestätiguns-E-mail erneut zu senden.',
        'success' => 'Dein Konto wurde erfolgreicht bestätigt!',
        'resent' => 'Bestätigungs-E-mail wurde erneut an die angegebene E-mail-Adresse gesendet .',
    ],

    'reset' => [
        'reset_button' => 'Passwort zurücksetzen',
        'password' => 'Passwort muss mindestens sechs Zeichen lang sein und den Bedingungen entsprechen.',
        'reset' => 'Dein Passwort wurde zurückgesetzt!',
        'sent' => 'Der Link zum Zurücksetzen deines Passworts wurde an deine E-mail-Adresse geschickt!',
        'token' => 'Passwort Zurücksetzungs-Token ist ungültig.',
        'user' => "Es wurde kein Benutzer mit dieser E-mail-Adresse gefunden.",
    ],

    'socialite' => [
        'unacceptable' => ':provider ist kein akzeptabler Anmeldetyp.',
    ]
];
