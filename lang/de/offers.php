<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Offers Language Lines
    |--------------------------------------------------------------------------
    */

    'general' => [
      'decline_reason' => 'Ablehnungsgrund',
      'decline_reason_empty' => 'Kein Grund angegeben',
      'decline_reason_offer_deleted' => 'Angebot gelöscht.',
      'decline_reason_another_offer' => 'Anderes Angebot akzeptiert.',
      'decline_reason_staff' => 'Von Mitarbeiter geschlossen.',
      'report' => 'Melden',
      'reported_by' => 'Gemeldet von <strong>:Username</strong>',
      'report_closed' => 'Geschlossen von :Username',
      'staff' => ':Page_name Mitarbeiter',
      'revoked' => 'Widerrufen',
      'enter_message' => 'Gib deine Nachricht ein...',
      'no_offers' => 'Es sind keine Angebote verfügbar.',
      'chat_buy' => 'Hey! Ich möchte dein :Game_name (:Platform_name) für :Price kaufen.',
      'chat_trade' => 'Hey! Ich möchte dein :Game_name (:Platform_name) gegen :Trade_game tauschen (:Trade_platform).',
      /* Start new strings v1.11 */
      'chat_sent' => 'verschickt',
      'chat_read' => 'gelesen',
      /* End new strings v1.11 */
    ],

    'status_wait' => [
      'wait' => 'Auf Antwort warten',
      'accept' => 'Akzeptieren',
      'decline' => 'Ablehnen',
    ],

    'status_rate' => [
      'rate_user' => 'Bewerte :Username',
      'rate_wait' => 'Warte auf die Bewertung von :Username',
    ],

    'status_complete' => [
      'rating_user' => 'Bewertung von :Username',
      'no_notice' => 'Kein Grund angegeben',
    ],

    'modal_accept' => [
      'title' => 'Angebot akzeptieren',
      'info' => "Du kannst diese Aktion nicht rückgängig machen. Alle anderen Angebote werden automatisch abgelehnt.",
    ],

    'modal_decline' => [
      'title' => 'Angebot ablehnen',
      'info' => "Du kannst diese Aktion nicht rückgängig machen.",
      'reason_placeholder' => 'Ablehnungsgrund (optional)',
    ],

    'modal_rating' => [
      'title_offer' => 'Schließe das Angebot & bewerte :Username',
      'title_listing' => 'Schließe den Eintrag & bewerte :Username',
      'negative' => 'Negativ',
      'neutral' => 'Neutral',
      'positive' => 'Positiv',
      'reason_placeholder' => 'Grund für Bewertung (optional)',
      'rate_button' => 'Bewerte :Username',
    ],

    /* Start new strings v1.1 */
    'modal_report' => [
      'title' =>  'Angebot melden',
      'describe_problem' => 'Beschreibe dein Problem',
      'info' => 'Bitte beschreibe dein Problem mit dem Angebot. Einer unserer Mitarbeiter wird dem Chat so schnell wie möglich beitreten.',
    ],
    /* End new strings v1.1 */

    /* Alerts */
    'alert' => [
      /* Start new strings v1.1 */
      'same_game' => 'Leider kannst du nicht das selbe Spiel vorschlagen!',
      'suggestion_disabled' => 'Leider kannst du keine Spiele vorschlagen!',
      'deleted' => ':Game_name Angebot gelöscht!',
      'reported' => 'Angebot gemeldet! Einer unserer Mitarbeiter wird den Chat so schnell wie möglich beitreten.',
      'already_reported' => 'Angebot wurde bereits von :Username gemeldet!',
      'missing_reason' => 'Bitte gib das Problem an, warum du dieses Angebot melden möchtest!',
      /* End new strings v1.1 */
      'own_offer' => "Leider kannst du keine Angebote zu deinen eigenen Einträgen abgeben!",
    ],

];
