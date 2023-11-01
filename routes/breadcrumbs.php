<?php

// Home
Breadcrumbs::for('home', function ($breadcrumbs) {
    $breadcrumbs->push(trans('general.home'), route('index'));
});

// Home > [User]
Breadcrumbs::for('profile', function ($breadcrumbs, $user) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(trans('general.breadcrumbs.profile', ['username' => $user->name]), route('profile', $user->name));
});

// Home > [Listings]
Breadcrumbs::for('listings', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(trans('general.listings'), route('listings'));
});

// Home > [Listings] -> [Platform]
Breadcrumbs::for('platform_listings', function ($breadcrumbs, $system) {
    $breadcrumbs->parent('listings');
    $breadcrumbs->push($system->name, route('listing', $system->acronym));
});

// Home > [Listings] -> [Platform] -> [Listing]
Breadcrumbs::for('listing', function ($breadcrumbs, $listing) {
    $breadcrumbs->parent('platform_listings', $listing->game->platform);
    $breadcrumbs->push(trans('general.breadcrumbs.listing', ['username' => $listing->user->name, 'gamename' => $listing->game->name, 'platform' => $listing->game->platform->name]), $listing->url_slug);
});

// Home > Games
Breadcrumbs::for('games', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(trans('general.games'), route('games'));
});

// Home > Games -> [Game]
Breadcrumbs::for('game', function ($breadcrumbs, $game) {
    $breadcrumbs->parent('games');
    $breadcrumbs->push($game->name.' ('.$game->platform->name.')', $game->url_slug);
});

// Home > [Search]
Breadcrumbs::for('search', function ($breadcrumbs, $value) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(trans('games.overview.search_result', ['value' => $value]), route('search', $value));
});

// Home > [Page]
Breadcrumbs::for('page', function ($breadcrumbs, $page) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push($page->title, route('page', $page->slug));
});

// Home > Blog
Breadcrumbs::for('blog', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(trans('general.blog'), route('blog'));
});

// Home > Blog > [Article]
Breadcrumbs::for('article', function ($breadcrumbs, $article) {
    $breadcrumbs->parent('blog');
    $breadcrumbs->push($article->title, route('blog'));
});
