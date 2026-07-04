<?php echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n"; ?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach ($sitemaps as $sm)
    <sitemap>
        <loc>{{ $sm['loc'] }}</loc>
        <lastmod>{{ $sm['lastmod'] }}</lastmod>
    </sitemap>
@endforeach
</sitemapindex>
