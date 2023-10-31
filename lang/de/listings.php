<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Listings Language Lines
    |--------------------------------------------------------------------------
    */

    'general' => [
      'newest_listings' => 'Neuste Einträge',
      'all_listings' => 'Alle Einträge',
      'listings' => 'Einträge',
      'sell' => 'Verkaufen',
      'trade' => 'Tauschen',
      'pickup' => 'Abholung',
      'delivery' => 'Versand',
      'condition' => 'Zustand',
      'digital_download' => 'Digitaler Download',
      'no_listings' => 'Es sind keine Einträge verfügbar.',
      'no_listings_add' => 'Füge den ersten Eintrag hinzu',
      'sold' => 'Nicht verfügbar',
      'show_all' => 'Zeige alle Einträge',
      'deleted' => 'Eintrag gelöscht.',
      /* Start new strings v1.11 */
      'deleted_game' => 'Spiel wurde aus dem System gelöscht.',
      'no_description' => 'Keine Beschreibung',
      /* End new strings v1.11 */
      /* 5 Condition Levels - 1 is worst -> 5 is best */
      'conditions' => [
          '5' => 'Neu',
          '4' => 'Wie neu',
          '3' => 'Gut',
          '2' => 'Akzeptabel',
          '1' => 'Nur CD vorhanden',
          '0' => 'Digitaler Download Code',
      ],
    ],

    'overview' => [
      'created' => 'Erstellt',
      'trade_info' => 'Bitte wähle das Spiel aus, das du gegen :Game_name tauschen möchtest.',
      'subheader' => [
          'buy_now' => 'Jetzt kaufen',
          'go_gameoverview' => 'Zur Spielübersicht',
          'details' => 'Details',
          'media' => 'Bilder & Videos',
      ],
    ],

    'form' => [
      'edit' => 'Eintrag bearbeiten',
      'add' => 'Eintrag hinzufügen',
      'game' => [
          'select' => 'Spiel auswählen',
          /* Start new strings v1.11 */
          'selected' => 'ausgewähltes Spiel',
          /* Start new strings v1.11 */
          'add' => 'Spiel hinzufügen',
          'not_found' => 'Spiel nicht gefunden?',
          'reselect' => 'Spiel neu auswählen',
          'reselect_info' => 'Warnung: Alle Eingaben werden zurückgesetzt!',
      ],
      'details_title' => 'Details',
      'details' => [
          'digital' => 'Digitaler Download',
          'limited' => 'Limited Edition',
          'description' => 'Beschreibung',
          'delivery_info' => 'Keine Eingabe für kostenlose Lieferung.',
      ],
      'sell_title' => 'Verkaufinfos',
      'sell' => [
          'price' => 'Preis',
          'price_suggestions' => 'Preisvorschläge',
      ],
      'trade_title' => 'Tauschinfos',
      'trade' => [
          'add_to_tradelist' => 'Füge Spiel zur Tauschliste hinzu',
          'remove' => 'Entfernen',
          'additional_charge_partner' => 'Zusätzlicher Aufpreis vom Tauschpartner',
          'additional_charge_self' => 'Zusätzlicher Aufpreis von dir',
          'trade_suggestions' => 'Tauschvorschläge',
      ],
      'placeholder' => [
          'sell_price_suggestion' => 'In :Currency_name...',
          'limited' => 'Name der Limited Edition',
          'description' => 'Beschreibe deinen Artikel (Optional)',
          'delivery' => 'Lieferkosten',
          'sell_price' => 'Preis in :Currency_name...',
          'additional_charge' => 'In :Currency_name...',
          'game_name' => 'Spielenamen eingeben...',
      ],
      'validation' => [
          'trade_list' => 'Du musst mindestens ein Spiel zu deiner Tauschliste hinzufügen.',
          'delivery_pickup' => 'Du musst mindestens eine Option auswählen.',
          'price' => 'Du musst einen gültigen Preis eingeben.',
          'no_game_found' => 'Leider wurde kein Spiel gefunden.',
          'no_game_found_add' => 'Neues Spiel zur Datenbank hinzufügen.',
      ],
      'add_button' => 'Eintrag erstellen',
      'save_button' => 'Eintrag speichern',
      /* Start new strings v1.4.0 */
      'image_upload' => [
          'images' => 'Bilder',
          'empty_message' => 'Bild-Dateien hier reinziehen oder zum auswählen klicken.',
          'max_files_exceeded' => 'Du kannst keine weiteren Dateien hochladen.',
          'already_exists' => 'Eine Datei mit diesem Namen existiert bereits in der Warteschlange.',
          'invalid_type' => 'Du kannst keine Datei mit diesem Typen hochladen.',
      ],
      /* Start new strings v1.4.0 */
    ],

    /* Start new strings v1.11 */
    'picture_upload' => [
        'picture' => 'Bild',
        'default' => 'Bild hier rein ziehen oder klicken',
        'replace' => 'Bild hier rein ziehen oder klicken zum ersetzen',
        'remove' => 'Entfernen',
        'error' => 'Ooops, da ist etwas schief gelaufen.',
        'error_filesize' => 'Die Datei ist zu groß',
        'error_minwidth' => 'Die Bildbreite ist zu klein',
        'error_maxwidth' => 'Die Bildbreite ist zu hoch',
        'error_minheight' => 'Die Bildhöhe ist zu klein',
        'error_maxheight' => 'Die Bildhöhe ist zu groß',
        'error_imageformat' => 'Dieses Bildformat ist nicht zulässig',
        'error_fileextension' => 'Dieses Dateiformat ist nicht zulässig',
    ],
    /* End new strings v1.11 */

    /* General modal translations */
    'modal' => [
      'close' => 'Schließen',
    ],

    /* Buy modal on listings overview */
    'modal_buy' => [
      'buy' => 'Kaufen',
      'buy_game' => ':Game_name <strong>kaufen</strong>',
      'total' => 'TOTAL',
      'delivery_free' => 'Kostenloser Versand',
      'delivery_price' => '+ :price Versand',
      'suggest_price' => 'Preis vorschlagen',
    ],

    /* Buy modal on listings overview */
    'modal_trade' => [
      'trade_game' => ':Game_name <strong>tauschen</strong>',
      'suggest' => 'Spiel vorschlagen',
    ],

    /* Add new game to database modal */
    'modal_game' => [
      'title' => 'Spiel zur Datenbank hinzufügen',
      'more' => 'Mehr',
      'search' => 'Suchen',
      'select_system' => 'Plattform auswählen',
      'searching' => 'Spiel wird gesucht suchen...',
      'adding' => 'Spiel wird in die :Pagename Datenbank hinzugefügt!',
      'wait' => 'Bitte warten',
      'placeholder' => [
          'value' => 'Gib einen Titel ein...',
      ],
    ],

    /* Alerts */
    'alert' => [
      'saved' => ':Game_name Eintrag gespeichert!',
      'deleted' => ':Game_name Eintrag gelöscht!',
      'created' => ':Game_name Eintrag erstellt!',
    ],

];
