<?php echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n"; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
@foreach ($urls as $u)
    <url>
        <loc>{{ $u['loc'] }}</loc>
@if (!empty($u['lastmod']))
        <lastmod>{{ $u['lastmod'] }}</lastmod>
@endif
@if (!empty($u['changefreq']))
        <changefreq>{{ $u['changefreq'] }}</changefreq>
@endif
@if (!empty($u['priority']))
        <priority>{{ $u['priority'] }}</priority>
@endif
@if (!empty($u['image']))
        <image:image>
            <image:loc>{{ $u['image'] }}</image:loc>
        </image:image>
@endif
    </url>
@endforeach
</urlset>
