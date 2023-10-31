<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Users Language Lines
    |--------------------------------------------------------------------------
    */

    'general' => [
      'profile' => 'Profil',
      'member_since' => 'Mitglied seit :time',
      'no_ratings' => 'Keine Bewertungen',
    ],

    'profile' => [
      'listings' => 'Einträge',
      'ratings' => 'Bewertungen',
      'stats' => 'Statistiken',
      'rating_from' => 'Bewertung von :username',
      'is_online' => ':username ist online',
      'last_seen' => 'zuletzt online :date',
      /* Start new strings v1.2 */
      'banned' => 'Benutzer gesperrt',
      /* End new strings v1.2 */
    ],


    /* Add new game to database modal */
    'dash' => [
      'dashboard' => 'Dashboard',
      'quick_listing' => 'Eintrag hinzufügen',
      'quick_game' => 'Spiel hinzufügen',
      'show_all' => 'Alle zeigen',
      'show_listings' => 'Zeig :count mehr aktive Einträge',
      'show_offers' => 'Zeig :count mehr aktive Angebote',
      'active' => 'Aktiv',
      'active_listings' => 'Aktive Einträge',
      'active_offers' => 'Aktive Angebote',
      'complete' => 'Vollständig',
      'deleted' => 'Gelöscht',
      'declined' => 'Abgelehnt',
      'stats' => [
          'stats' => 'Statistiken',
          'earned_money' => 'Geld verdient',
          'spend_money' => 'Geld ausgegeben',
          'clicks_listings' => 'Auf Einträge geklickt',
          'created_listings' => 'Einträge erstellt',
          'made_offers' => 'Angebote gemacht',
          'membership' => 'Mitgliedschaft',
      ],
      /* & offers */
      'listings' => [
          'status_0' => 'Warten',
          'status_1' => 'Bewerte :Username',
          'status_1_wait' => 'Auf Bewertung warten',
          'no_offers' => 'Derzeit keine Angebote.',
          'clicks' => 'Klicks',
      ],
      'settings' => [
          'settings' => 'Einstellungen',
          'password' => 'Passwort',
          'password_heading' => 'Passwort ändern',
          'password_old' => 'Passwort ändern',
          'password_new' => 'Neues Passwort',
          'password_new_confirm' => 'Neues Passwort bestätigen',
          'profile' => 'Profil',
          'profile_link' => 'Dein Profil Link:',
          'username' => 'Benutzername',
          'email' => 'E-Mail-Addresse',
          'change_avatar' => 'Profilbild ändern',
          'browse' => 'Durchsuchen',
          'location_change' => 'Standort ändern',
          'location_set' => 'Standort festlegen',
          'location_no' => 'Kein Standort festgelegt',
      ],
    ],

    /* Alerts */
    'alert' => [
      'password_changed' => 'Dein Passwort wurde erfolgreich geändert',
      'profile_saved' => 'Dein Profil wurde erfolgreich gespeichert.',
      'email_taken' => 'Dies E-Mail-Adresse wird bereits verwendet.',
    ],

    /* Modal for delete listing in dashboard */
    'modal_delete_listing' => [
      'title' => 'Lösche :Gamename Eintrag',
      'info' => 'Bist du sicher, dass du diesen Eintrag löschen möchtest? ',
      'delete_listing' => 'Eintrag löschen',
    ],


    /* Modal for delete listing in dashboard */
    'modal_delete_offer' => [
      'title' => 'Lösche :Gamename Angebot',
      'info' => 'Bist du sicher, dass du dieses Angebot löschen möchtest? ',
      'delete_listing' => 'Angebot löschen',
    ],

    /* Add new game to database modal */
    'modal_location' => [
      'title' => 'Standort festlegn',
      'set_location' => 'Standort festlegen',
      'info' => "Keine Sorge! In ein paar Sekunden kannst du deinen ersten Eintrag auf GameTrade hinzufügen. Aber zuerst musst du deinen Standort festlegen. Bitte wähle dein Land aus und gib deine Postleitzahl ein um deinen Standort festzulegen.",
      'selected_location' => 'Standort festgelegt',
      'location_saved' => 'Standort gespeichert!',
      /* JS Counter between close_sec_1 and close_sec 2 */
      'close_sec_1' => 'Das Fensters schließt in',
      'close_sec_2' => 'Sekunden automatisch oder',
      'close_now' => 'Jetzt schließen',
      'error' => 'Ein Fehler ist aufgetreten, bitte versuche es nochmal.',
      'placeholder' => [
          'country' => 'Wähle dein Land aus',
          'postal_code' => 'Postleitzahl',
          'postal_code_locality' => 'Gib deine Postleitzahl ein',
          /* Start new strings v1.11 */
          'where_are_we_going' => 'Wohin gehts?',
          /* End new strings v1.11 */
      ],
      'status' => [
          'search_info' => ' Gib mindestens 3 Zeichen ein um die Suche zu beginnen.',
          'searching' => 'Standort suchen...',
          'searching_place' => 'Ort suchen...',
          'location_found' => 'Standort gefunden!',
          'locations_found' => 'Standort gefunden!',
          'no_location_found' => 'Keinen Standort gefunden',
      ],
    ],

];
