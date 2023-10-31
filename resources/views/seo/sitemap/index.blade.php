@php echo '<?xml version="1.0" encoding="UTF-8"?>'; @endphp
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <sitemap>
        <loc>{{ url('sitemap/listings') }}</loc>
        @if($listing)
            <lastmod>{{ $listing->updated_at->tz('UTC')->toAtomString() }}</lastmod>
        @endif
    </sitemap>
    <sitemap>
      <loc>{{ url('sitemap/games') }}</loc>
        @if($games)
            <lastmod>{{ $games->updated_at->tz('UTC')->toAtomString() }}</lastmod>
        @endif
    </sitemap>
</sitemapindex>
