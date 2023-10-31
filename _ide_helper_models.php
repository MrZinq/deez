<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */

namespace App\Models{
    /**
     * App\Models\Article.
     *
     * @property int $id
     * @property int $category_id
     * @property string $title
     * @property string $slug
     * @property string $content
     * @property string|null $image
     * @property string $status
     * @property \Illuminate\Support\Carbon $date
     * @property bool $featured
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property string|null $deleted_at
     * @property-read \App\Models\Category|null $category
     * @property-read mixed $image_large
     * @property-read mixed $image_square_tiny
     * @property-read mixed $slug_or_title
     * @property-read mixed $url_slug
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tag[] $tags
     * @property-read int|null $tags_count
     * @method static \Illuminate\Database\Eloquent\Builder|Article findSimilarSlugs(string $attribute, array $config, string $slug)
     * @method static \Illuminate\Database\Eloquent\Builder|Article newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Article newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Article published()
     * @method static \Illuminate\Database\Eloquent\Builder|Article query()
     * @method static \Illuminate\Database\Eloquent\Builder|Article whereCategoryId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Article whereContent($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Article whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Article whereDate($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Article whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Article whereFeatured($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Article whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Article whereImage($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Article whereSlug($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Article whereStatus($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Article whereTitle($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Article whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Article withUniqueSlugConstraints(\Illuminate\Database\Eloquent\Model $model, string $attribute, array $config, string $slug)
     * @mixin \Eloquent
     */
    class IdeHelperArticle
    {
    }
}

namespace App\Models{
    /**
     * App\Models\Category.
     *
     * @property int $id
     * @property int|null $parent_id
     * @property int|null $lft
     * @property int|null $rgt
     * @property int|null $depth
     * @property string $name
     * @property string $slug
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property string|null $deleted_at
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Article[] $articles
     * @property-read int|null $articles_count
     * @property-read \Illuminate\Database\Eloquent\Collection|Category[] $children
     * @property-read int|null $children_count
     * @property-read mixed $slug_or_name
     * @property-read Category|null $parent
     * @method static \Illuminate\Database\Eloquent\Builder|Category findSimilarSlugs(string $attribute, array $config, string $slug)
     * @method static \Illuminate\Database\Eloquent\Builder|Category firstLevelItems()
     * @method static \Illuminate\Database\Eloquent\Builder|Category newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Category newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Category query()
     * @method static \Illuminate\Database\Eloquent\Builder|Category whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Category whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Category whereDepth($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Category whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Category whereLft($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Category whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Category whereParentId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Category whereRgt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Category whereSlug($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Category whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Category withUniqueSlugConstraints(\Illuminate\Database\Eloquent\Model $model, string $attribute, array $config, string $slug)
     * @mixin \Eloquent
     */
    class IdeHelperCategory
    {
    }
}

namespace App\Models{
    /**
     * App\Models\Comment.
     *
     * @property int $id
     * @property int $commentable_id
     * @property string $commentable_type
     * @property int $user_id
     * @property string $content
     * @property int $likes
     * @property int|null $status
     * @property int|null $has_children
     * @property int|null $root_id
     * @property string|null $last_reply_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property-read \App\Models\Article|null $article
     * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $commentable
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CommentLike[] $dblikes
     * @property-read int|null $dblikes_count
     * @property-read \App\Models\Game|null $game
     * @property-read mixed $type
     * @property-read \App\Models\Listing|null $listing
     * @property-read \App\Models\User $user
     * @method static \Illuminate\Database\Eloquent\Builder|Comment newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Comment newQuery()
     * @method static \Illuminate\Database\Query\Builder|Comment onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|Comment query()
     * @method static \Illuminate\Database\Eloquent\Builder|Comment whereCommentableId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Comment whereCommentableType($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Comment whereContent($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Comment whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Comment whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Comment whereHasChildren($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Comment whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Comment whereLastReplyAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Comment whereLikes($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Comment whereRootId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Comment whereStatus($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Comment whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Comment whereUserId($value)
     * @method static \Illuminate\Database\Query\Builder|Comment withTrashed()
     * @method static \Illuminate\Database\Query\Builder|Comment withoutTrashed()
     * @mixin \Eloquent
     */
    class IdeHelperComment
    {
    }
}

namespace App\Models{
    /**
     * App\Models\CommentLike.
     *
     * @property int $id
     * @property int $comment_id
     * @property int $user_id
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property-read \App\Models\Comment $comment
     * @property-read \App\Models\User $user
     * @method static \Illuminate\Database\Eloquent\Builder|CommentLike newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|CommentLike newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|CommentLike query()
     * @method static \Illuminate\Database\Eloquent\Builder|CommentLike whereCommentId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|CommentLike whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|CommentLike whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|CommentLike whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|CommentLike whereUserId($value)
     * @mixin \Eloquent
     */
    class IdeHelperCommentLike
    {
    }
}

namespace App\Models{
    /**
     * App\Models\Country.
     *
     * @property int $id
     * @property string $name
     * @property string|null $native
     * @property string $code
     * @property int|null $parent_id
     * @property int|null $lft
     * @property int|null $rgt
     * @property int|null $depth
     * @property string|null $deleted_at
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @method static \Illuminate\Database\Eloquent\Builder|Country newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Country newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Country query()
     * @method static \Illuminate\Database\Eloquent\Builder|Country whereCode($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Country whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Country whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Country whereDepth($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Country whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Country whereLft($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Country whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Country whereNative($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Country whereParentId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Country whereRgt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Country whereUpdatedAt($value)
     * @mixin \Eloquent
     */
    class IdeHelperCountry
    {
    }
}

namespace App\Models{
    /**
     * App\Models\Digital.
     *
     * @property int $id
     * @property string $name
     * @property string|null $description
     * @property string|null $deleted_at
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @method static \Illuminate\Database\Eloquent\Builder|Digital newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Digital newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Digital query()
     * @method static \Illuminate\Database\Eloquent\Builder|Digital whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Digital whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Digital whereDescription($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Digital whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Digital whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Digital whereUpdatedAt($value)
     * @mixin \Eloquent
     */
    class IdeHelperDigital
    {
    }
}

namespace App\Models{
    /**
     * App\Models\Game.
     *
     * @property int $id
     * @property string $name
     * @property string|null $cover
     * @property int $cover_generator
     * @property string|null $description
     * @property \Illuminate\Support\Carbon|null $release_date
     * @property string|null $publisher
     * @property string|null $developer
     * @property string|null $pegi
     * @property string|null $tags
     * @property string|null $source_name
     * @property int|null $metacritic_id
     * @property int|null $giantbomb_id
     * @property int $platform_id
     * @property int|null $genre_id
     * @property \Illuminate\Support\Carbon|null $deleted_at
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property-read \App\Models\Listing|null $averagePrice
     * @property-read \App\Models\Listing|null $cheapestListing
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comment[] $comments
     * @property-read int|null $comments_count
     * @property-read \App\Models\Genre|null $genre
     * @property-read mixed $cheapest_listing
     * @property-read mixed $highest_price
     * @property-read mixed $image_carousel
     * @property-read mixed $image_cover
     * @property-read mixed $image_square
     * @property-read mixed $image_square_tiny
     * @property-read int|null $listings_count
     * @property-read mixed $lowest_price
     * @property-read mixed $url_slug
     * @property-read mixed $wishlist_count
     * @property-read \App\Models\Giantbomb|null $giantbomb
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Wishlist[] $heartbeat
     * @property-read int|null $heartbeat_count
     * @property-read \App\Models\Listing|null $highestListing
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Listing[] $listings
     * @property-read \App\Models\Listing|null $listingsCount
     * @property-read \App\Models\Metacritic|null $metacritic
     * @property-read \App\Models\Platform $platform
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Listing[] $tradegames
     * @property-read int|null $tradegames_count
     * @property-read \App\Models\Wishlist|null $wishlist
     * @property-read \App\Models\Wishlist|null $wishlistCount
     * @method static \Illuminate\Database\Eloquent\Builder|Game newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Game newQuery()
     * @method static \Illuminate\Database\Query\Builder|Game onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|Game query()
     * @method static \Illuminate\Database\Eloquent\Builder|Game whereCover($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Game whereCoverGenerator($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Game whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Game whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Game whereDescription($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Game whereDeveloper($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Game whereGenreId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Game whereGiantbombId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Game whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Game whereMetacriticId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Game whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Game wherePegi($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Game wherePlatformId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Game wherePublisher($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Game whereReleaseDate($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Game whereSourceName($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Game whereTags($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Game whereUpdatedAt($value)
     * @method static \Illuminate\Database\Query\Builder|Game withTrashed()
     * @method static \Illuminate\Database\Query\Builder|Game withoutTrashed()
     * @mixin \Eloquent
     */
    class IdeHelperGame
    {
    }
}

namespace App\Models{
    /**
     * App\Models\Genre.
     *
     * @property int $id
     * @property string $name
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Game[] $games
     * @property-read int|null $games_count
     * @method static \Illuminate\Database\Eloquent\Builder|Genre newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Genre newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Genre query()
     * @method static \Illuminate\Database\Eloquent\Builder|Genre whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Genre whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Genre whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Genre whereUpdatedAt($value)
     * @mixin \Eloquent
     */
    class IdeHelperGenre
    {
    }
}

namespace App\Models{
    /**
     * App\Models\Giantbomb.
     *
     * @property int $id
     * @property string $name
     * @property string|null $summary
     * @property string|null $genres
     * @property string|null $image
     * @property string|null $images
     * @property string|null $videos
     * @property string|null $ratings
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @method static \Illuminate\Database\Eloquent\Builder|Giantbomb newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Giantbomb newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Giantbomb query()
     * @method static \Illuminate\Database\Eloquent\Builder|Giantbomb whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Giantbomb whereGenres($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Giantbomb whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Giantbomb whereImage($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Giantbomb whereImages($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Giantbomb whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Giantbomb whereRatings($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Giantbomb whereSummary($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Giantbomb whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Giantbomb whereVideos($value)
     * @mixin \Eloquent
     */
    class IdeHelperGiantbomb
    {
    }
}

namespace App\Models{
    /**
     * App\Models\Language.
     *
     * @property int $id
     * @property string $name
     * @property string $app_name
     * @property string|null $flag
     * @property string $abbr
     * @property string|null $script
     * @property string|null $native
     * @property int $active
     * @property int $default
     * @property string|null $created_at
     * @property string|null $updated_at
     * @property string|null $deleted_at
     * @method static \Illuminate\Database\Eloquent\Builder|Language newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Language newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Language query()
     * @method static \Illuminate\Database\Eloquent\Builder|Language whereAbbr($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Language whereActive($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Language whereAppName($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Language whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Language whereDefault($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Language whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Language whereFlag($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Language whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Language whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Language whereNative($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Language whereScript($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Language whereUpdatedAt($value)
     * @mixin \Eloquent
     */
    class IdeHelperLanguage
    {
    }
}

namespace App\Models{
    /**
     * App\Models\Listing.
     *
     * @property int $id
     * @property int $user_id
     * @property int $game_id
     * @property string|null $name
     * @property string|null $picture
     * @property string|null $description
     * @property int|null $price
     * @property int|null $condition
     * @property int|null $digital
     * @property string|null $limited_edition
     * @property int $delivery
     * @property int|null $delivery_price
     * @property int $pickup
     * @property int $sell
     * @property int $sell_negotiate
     * @property int $trade
     * @property int $trade_negotiate
     * @property string|null $trade_list
     * @property int $payment
     * @property int|null $status
     * @property int $clicks
     * @property string $last_offer_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property-read \App\Models\Game $game
     * @property-read mixed $condition_string
     * @property-read mixed $delivery_price_formatted
     * @property-read mixed $distance
     * @property-read mixed $picture_original
     * @property-read mixed $picture_square
     * @property-read mixed $price_decimal
     * @property-read mixed $price_formatted
     * @property-read mixed $url_slug
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ListingImage[] $images
     * @property-read int|null $images_count
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Offer[] $offers
     * @property-read int|null $offers_count
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Game[] $tradegames
     * @property-read int|null $tradegames_count
     * @property-read \App\Models\User $user
     * @method static \Illuminate\Database\Eloquent\Builder|Listing distanceto($latitude, $longitude)
     * @method static \Illuminate\Database\Eloquent\Builder|Listing geofence($latitude, $longitude, $inner_radius, $outer_radius)
     * @method static \Illuminate\Database\Eloquent\Builder|Listing newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Listing newQuery()
     * @method static \Illuminate\Database\Query\Builder|Listing onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|Listing query()
     * @method static \Illuminate\Database\Eloquent\Builder|Listing whereClicks($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Listing whereCondition($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Listing whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Listing whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Listing whereDelivery($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Listing whereDeliveryPrice($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Listing whereDescription($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Listing whereDigital($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Listing whereGameId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Listing whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Listing whereLastOfferAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Listing whereLimitedEdition($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Listing whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Listing wherePayment($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Listing wherePickup($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Listing wherePicture($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Listing wherePrice($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Listing whereSell($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Listing whereSellNegotiate($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Listing whereStatus($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Listing whereTrade($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Listing whereTradeList($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Listing whereTradeNegotiate($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Listing whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Listing whereUserId($value)
     * @method static \Illuminate\Database\Query\Builder|Listing withTrashed()
     * @method static \Illuminate\Database\Query\Builder|Listing withoutTrashed()
     * @mixin \Eloquent
     */
    class IdeHelperListing
    {
    }
}

namespace App\Models{
    /**
     * App\Models\ListingImage.
     *
     * @property int $id
     * @property int $listing_id
     * @property int $user_id
     * @property string $filename
     * @property int $order
     * @property int $default
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property-read mixed $thumbnail
     * @property-read mixed $url
     * @property-read \App\Models\Listing $listing
     * @method static \Illuminate\Database\Eloquent\Builder|ListingImage newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|ListingImage newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|ListingImage query()
     * @method static \Illuminate\Database\Eloquent\Builder|ListingImage whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ListingImage whereDefault($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ListingImage whereFilename($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ListingImage whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ListingImage whereListingId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ListingImage whereOrder($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ListingImage whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ListingImage whereUserId($value)
     * @mixin \Eloquent
     */
    class IdeHelperListingImage
    {
    }
}

namespace App\Models{
    /**
     * App\Models\MenuItem.
     *
     * @property int $id
     * @property string $name
     * @property string|null $type
     * @property string|null $link
     * @property int|null $page_id
     * @property int|null $parent_id
     * @property int|null $lft
     * @property int|null $rgt
     * @property int|null $depth
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property string|null $deleted_at
     * @property-read \Illuminate\Database\Eloquent\Collection|MenuItem[] $children
     * @property-read int|null $children_count
     * @property-read \App\Models\Page|null $page
     * @property-read MenuItem|null $parent
     * @method static \Illuminate\Database\Eloquent\Builder|MenuItem newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|MenuItem newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|MenuItem query()
     * @method static \Illuminate\Database\Eloquent\Builder|MenuItem whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|MenuItem whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|MenuItem whereDepth($value)
     * @method static \Illuminate\Database\Eloquent\Builder|MenuItem whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|MenuItem whereLft($value)
     * @method static \Illuminate\Database\Eloquent\Builder|MenuItem whereLink($value)
     * @method static \Illuminate\Database\Eloquent\Builder|MenuItem whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder|MenuItem wherePageId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|MenuItem whereParentId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|MenuItem whereRgt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|MenuItem whereType($value)
     * @method static \Illuminate\Database\Eloquent\Builder|MenuItem whereUpdatedAt($value)
     * @mixin \Eloquent
     */
    class IdeHelperMenuItem
    {
    }
}

namespace App\Models{
    /**
     * App\Models\Metacritic.
     *
     * @property int $id
     * @property int $game_id
     * @property string $name
     * @property int|null $score
     * @property int|null $userscore
     * @property string $thumbnail
     * @property string $summary
     * @property string|null $genre
     * @property string $platform
     * @property string $publisher
     * @property string $developer
     * @property string $rating
     * @property string $release_date
     * @property string $url
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property-read mixed $score_class
     * @method static \Illuminate\Database\Eloquent\Builder|Metacritic newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Metacritic newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Metacritic query()
     * @method static \Illuminate\Database\Eloquent\Builder|Metacritic whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Metacritic whereDeveloper($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Metacritic whereGameId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Metacritic whereGenre($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Metacritic whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Metacritic whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Metacritic wherePlatform($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Metacritic wherePublisher($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Metacritic whereRating($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Metacritic whereReleaseDate($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Metacritic whereScore($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Metacritic whereSummary($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Metacritic whereThumbnail($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Metacritic whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Metacritic whereUrl($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Metacritic whereUserscore($value)
     * @mixin \Eloquent
     */
    class IdeHelperMetacritic
    {
    }
}

namespace App\Models{
    /**
     * App\Models\Offer.
     *
     * @property int $id
     * @property int $status
     * @property int $user_id
     * @property int $listing_id
     * @property int|null $thread_id
     * @property string|null $note
     * @property int|null $price_offer
     * @property int|null $trade_game
     * @property string|null $additional_type
     * @property int|null $additional_charge
     * @property int $delivery
     * @property int|null $trade_from_list
     * @property int $declined
     * @property string|null $decline_note
     * @property int|null $rating_id_offer
     * @property int|null $rating_id_listing
     * @property string|null $closed_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property-read \App\Models\Game|null $game
     * @property-read mixed $price_offer_formatted
     * @property-read mixed $reported
     * @property-read mixed $url
     * @property-read \App\Models\Listing $listing
     * @property-read \App\Models\Payment|null $payment
     * @property-read \App\Models\Report|null $report
     * @property-read \Cmgmyr\Messenger\Models\Thread|null $thread
     * @property-read \App\Models\User $user
     * @method static \Illuminate\Database\Eloquent\Builder|Offer newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Offer newQuery()
     * @method static \Illuminate\Database\Query\Builder|Offer onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|Offer query()
     * @method static \Illuminate\Database\Eloquent\Builder|Offer whereAdditionalCharge($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Offer whereAdditionalType($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Offer whereClosedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Offer whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Offer whereDeclineNote($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Offer whereDeclined($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Offer whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Offer whereDelivery($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Offer whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Offer whereListingId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Offer whereNote($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Offer wherePriceOffer($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Offer whereRatingIdListing($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Offer whereRatingIdOffer($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Offer whereStatus($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Offer whereThreadId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Offer whereTradeFromList($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Offer whereTradeGame($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Offer whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Offer whereUserId($value)
     * @method static \Illuminate\Database\Query\Builder|Offer withTrashed()
     * @method static \Illuminate\Database\Query\Builder|Offer withoutTrashed()
     * @mixin \Eloquent
     */
    class IdeHelperOffer
    {
    }
}

namespace App\Models{
    /**
     * App\Models\Page.
     *
     * @property int $id
     * @property string $template
     * @property string $name
     * @property string $title
     * @property string $slug
     * @property string|null $content
     * @property array|null $extras
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property string|null $deleted_at
     * @property-read mixed $slug_or_title
     * @method static \Illuminate\Database\Eloquent\Builder|Page findSimilarSlugs(string $attribute, array $config, string $slug)
     * @method static \Illuminate\Database\Eloquent\Builder|Page newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Page newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Page query()
     * @method static \Illuminate\Database\Eloquent\Builder|Page whereContent($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Page whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Page whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Page whereExtras($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Page whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Page whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Page whereSlug($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Page whereTemplate($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Page whereTitle($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Page whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Page withUniqueSlugConstraints(\Illuminate\Database\Eloquent\Model $model, string $attribute, array $config, string $slug)
     * @mixin \Eloquent
     */
    class IdeHelperPage
    {
    }
}

namespace App\Models{
    /**
     * App\Models\Payment.
     *
     * @property int $id
     * @property int $item_id
     * @property string $item_type
     * @property int $user_id
     * @property string $transaction_id
     * @property string $payment_method
     * @property string $payer_info
     * @property float $total
     * @property float|null $transaction_fee
     * @property string $currency
     * @property int $status
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $item
     * @property-read \App\Models\Offer|null $offer
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Transaction[] $transactions
     * @property-read int|null $transactions_count
     * @property-read \App\Models\User $user
     * @method static \Illuminate\Database\Eloquent\Builder|Payment newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Payment newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Payment query()
     * @method static \Illuminate\Database\Eloquent\Builder|Payment whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Payment whereCurrency($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Payment whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Payment whereItemId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Payment whereItemType($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Payment wherePayerInfo($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Payment wherePaymentMethod($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Payment whereStatus($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Payment whereTotal($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Payment whereTransactionFee($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Payment whereTransactionId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Payment whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Payment whereUserId($value)
     * @mixin \Eloquent
     */
    class IdeHelperPayment
    {
    }
}

namespace App\Models{
    /**
     * App\Models\Platform.
     *
     * @property int $id
     * @property string $name
     * @property string|null $description
     * @property string|null $color
     * @property string $acronym
     * @property string $cover_position
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Digital[] $digitals
     * @property-read int|null $digitals_count
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Game[] $games
     * @property-read int|null $games_count
     * @property-read \App\Models\Game|null $gamesCount
     * @property-read mixed $url
     * @method static \Illuminate\Database\Eloquent\Builder|Platform newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Platform newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Platform query()
     * @method static \Illuminate\Database\Eloquent\Builder|Platform whereAcronym($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Platform whereColor($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Platform whereCoverPosition($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Platform whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Platform whereDescription($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Platform whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Platform whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Platform whereUpdatedAt($value)
     * @mixin \Eloquent
     */
    class IdeHelperPlatform
    {
    }
}

namespace App\Models{
    /**
     * App\Models\Report.
     *
     * @property int $id
     * @property int $status
     * @property int $offer_id
     * @property int $listing_id
     * @property int $user_id
     * @property string|null $user_is
     * @property string|null $reason
     * @property int|null $user_staff
     * @property \Illuminate\Support\Carbon|null $closed_at
     * @property string|null $deleted_at
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property-read \App\Models\Offer $offer
     * @property-read \App\Models\User|null $staff
     * @property-read \App\Models\User $user
     * @method static \Illuminate\Database\Eloquent\Builder|Report newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Report newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Report query()
     * @method static \Illuminate\Database\Eloquent\Builder|Report whereClosedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Report whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Report whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Report whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Report whereListingId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Report whereOfferId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Report whereReason($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Report whereStatus($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Report whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Report whereUserId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Report whereUserIs($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Report whereUserStaff($value)
     * @mixin \Eloquent
     */
    class IdeHelperReport
    {
    }
}

namespace App\Models{
    /**
     * Class SocialLogin.
     *
     * @property int $id
     * @property int $user_id
     * @property string $provider
     * @property string $provider_id
     * @property string|null $token
     * @property string|null $avatar
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @method static \Illuminate\Database\Eloquent\Builder|SocialLogin newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|SocialLogin newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|SocialLogin query()
     * @method static \Illuminate\Database\Eloquent\Builder|SocialLogin whereAvatar($value)
     * @method static \Illuminate\Database\Eloquent\Builder|SocialLogin whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|SocialLogin whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|SocialLogin whereProvider($value)
     * @method static \Illuminate\Database\Eloquent\Builder|SocialLogin whereProviderId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|SocialLogin whereToken($value)
     * @method static \Illuminate\Database\Eloquent\Builder|SocialLogin whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|SocialLogin whereUserId($value)
     * @mixin \Eloquent
     */
    class IdeHelperSocialLogin
    {
    }
}

namespace App\Models{
    /**
     * App\Models\Tag.
     *
     * @property int $id
     * @property string $name
     * @property string $slug
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property string|null $deleted_at
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Article[] $articles
     * @property-read int|null $articles_count
     * @property-read mixed $slug_or_name
     * @method static \Illuminate\Database\Eloquent\Builder|Tag findSimilarSlugs(string $attribute, array $config, string $slug)
     * @method static \Illuminate\Database\Eloquent\Builder|Tag newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Tag newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Tag query()
     * @method static \Illuminate\Database\Eloquent\Builder|Tag whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Tag whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Tag whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Tag whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Tag whereSlug($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Tag whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Tag withUniqueSlugConstraints(\Illuminate\Database\Eloquent\Model $model, string $attribute, array $config, string $slug)
     * @mixin \Eloquent
     */
    class IdeHelperTag
    {
    }
}

namespace App\Models{
    /**
     * App\Models\Transaction.
     *
     * @property int $id
     * @property string $type
     * @property int $item_id
     * @property string $item_type
     * @property int $user_id
     * @property int|null $payment_id
     * @property int|null $payer_id
     * @property float $total
     * @property string $currency
     * @property int $status
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $item
     * @property-read \App\Models\Offer|null $offer
     * @property-read \App\Models\User|null $payer
     * @property-read \App\Models\User $user
     * @property-read \App\Models\Withdrawal|null $withdrawal
     * @method static \Illuminate\Database\Eloquent\Builder|Transaction newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Transaction newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Transaction query()
     * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereCurrency($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereItemId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereItemType($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Transaction wherePayerId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Transaction wherePaymentId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereStatus($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereTotal($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereType($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereUserId($value)
     * @mixin \Eloquent
     */
    class IdeHelperTransaction
    {
    }
}

namespace App\Models{
    /**
     * App\Models\User.
     *
     * @property int $id
     * @property string $name
     * @property string $email
     * @property string|null $password
     * @property string|null $avatar
     * @property int|null $status
     * @property string $confirmation_code
     * @property int $confirmed
     * @property float $balance
     * @property string|null $remember_token
     * @property \Illuminate\Support\Carbon|null $last_activity_at
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     * @property-read mixed $avatar_square
     * @property-read mixed $avatar_square_tiny
     * @property-read mixed $negative_ratings
     * @property-read mixed $neutral_ratings
     * @property-read mixed $positive_percent_ratings
     * @property-read mixed $positive_ratings
     * @property-read mixed $url
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Listing[] $listings
     * @property-read int|null $listings_count
     * @property-read \App\Models\User_Location|null $location
     * @property-read \Illuminate\Database\Eloquent\Collection|\Cmgmyr\Messenger\Models\Message[] $messages
     * @property-read int|null $messages_count
     * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
     * @property-read int|null $notifications_count
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Offer[] $offers
     * @property-read int|null $offers_count
     * @property-read \Illuminate\Database\Eloquent\Collection|\Cmgmyr\Messenger\Models\Participant[] $participants
     * @property-read int|null $participants_count
     * @property-read \Illuminate\Database\Eloquent\Collection|\Wiledia\Backport\Auth\Database\Permission[] $permissions
     * @property-read int|null $permissions_count
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SocialLogin[] $providers
     * @property-read int|null $providers_count
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User_Rating[] $ratings
     * @property-read int|null $ratings_count
     * @property-read \Illuminate\Database\Eloquent\Collection|\Wiledia\Backport\Auth\Database\Role[] $roles
     * @property-read int|null $roles_count
     * @property-read \Illuminate\Database\Eloquent\Collection|\Cmgmyr\Messenger\Models\Thread[] $threads
     * @property-read int|null $threads_count
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Transaction[] $transactions
     * @property-read int|null $transactions_count
     * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
     * @method static \Illuminate\Database\Query\Builder|User onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|User query()
     * @method static \Illuminate\Database\Eloquent\Builder|User whereAvatar($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User whereBalance($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User whereConfirmationCode($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User whereConfirmed($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User whereLastActivityAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User whereStatus($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
     * @method static \Illuminate\Database\Query\Builder|User withTrashed()
     * @method static \Illuminate\Database\Query\Builder|User withoutTrashed()
     * @mixin \Eloquent
     */
    class IdeHelperUser
    {
    }
}

namespace App\Models{
    /**
     * App\Models\User_Location.
     *
     * @property int $id
     * @property int $user_id
     * @property string $country
     * @property string $country_abbreviation
     * @property string $postal_code
     * @property string $place
     * @property float|null $longitude
     * @property float|null $latitude
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @method static \Illuminate\Database\Eloquent\Builder|User_Location newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|User_Location newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|User_Location query()
     * @method static \Illuminate\Database\Eloquent\Builder|User_Location whereCountry($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User_Location whereCountryAbbreviation($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User_Location whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User_Location whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User_Location whereLatitude($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User_Location whereLongitude($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User_Location wherePlace($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User_Location wherePostalCode($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User_Location whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User_Location whereUserId($value)
     * @mixin \Eloquent
     */
    class IdeHelperUser_Location
    {
    }
}

namespace App\Models{
    /**
     * App\Models\User_Rating.
     *
     * @property int $id
     * @property int $user_id_from
     * @property int $user_id_to
     * @property int $is_seller
     * @property int $offer_id
     * @property int $listing_id
     * @property int $rating
     * @property string|null $notice
     * @property int $active
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property-read \App\Models\Offer $offer
     * @property-read \App\Models\User $user_from
     * @property-read \App\Models\User $user_to
     * @method static \Illuminate\Database\Eloquent\Builder|User_Rating newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|User_Rating newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|User_Rating query()
     * @method static \Illuminate\Database\Eloquent\Builder|User_Rating whereActive($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User_Rating whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User_Rating whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User_Rating whereIsSeller($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User_Rating whereListingId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User_Rating whereNotice($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User_Rating whereOfferId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User_Rating whereRating($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User_Rating whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User_Rating whereUserIdFrom($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User_Rating whereUserIdTo($value)
     * @mixin \Eloquent
     */
    class IdeHelperUser_Rating
    {
    }
}

namespace App\Models{
    /**
     * App\Models\Wishlist.
     *
     * @property int $id
     * @property int $game_id
     * @property int $user_id
     * @property int $notification
     * @property int|null $max_price
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property-read \App\Models\Game $game
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Listing[] $listings
     * @property-read int|null $listings_count
     * @property-read \App\Models\User $user
     * @method static \Illuminate\Database\Eloquent\Builder|Wishlist newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Wishlist newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Wishlist query()
     * @method static \Illuminate\Database\Eloquent\Builder|Wishlist whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Wishlist whereGameId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Wishlist whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Wishlist whereMaxPrice($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Wishlist whereNotification($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Wishlist whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Wishlist whereUserId($value)
     * @mixin \Eloquent
     */
    class IdeHelperWishlist
    {
    }
}

namespace App\Models{
    /**
     * App\Models\Withdrawal.
     *
     * @property int $id
     * @property int $user_id
     * @property float $total
     * @property string $currency
     * @property string $payment_method
     * @property string $payment_details
     * @property int $status
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property-read \App\Models\User $user
     * @method static \Illuminate\Database\Eloquent\Builder|Withdrawal newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Withdrawal newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Withdrawal query()
     * @method static \Illuminate\Database\Eloquent\Builder|Withdrawal whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Withdrawal whereCurrency($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Withdrawal whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Withdrawal wherePaymentDetails($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Withdrawal wherePaymentMethod($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Withdrawal whereStatus($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Withdrawal whereTotal($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Withdrawal whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Withdrawal whereUserId($value)
     * @mixin \Eloquent
     */
    class IdeHelperWithdrawal
    {
    }
}
