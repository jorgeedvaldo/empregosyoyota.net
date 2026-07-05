<?php echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n"; ?>
<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
	xmlns:georss="http://www.georss.org/georss"
	xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#"
	>

    <channel>
        <title>Empregos Yoyota</title>
        <atom:link href="{{ url('/feed') }}" rel="self" type="application/rss+xml" />
        <link>{{ url('/') }}</link>
        <description>Vagas de emprego em Angola, Brasil e Moçambique, estágios, bolsas de estudo e artigos</description>
        <lastBuildDate>{{ $items->isNotEmpty() ? $items->first()['pubDate'] : now()->format(DATE_ATOM) }}</lastBuildDate>
        <language>pt-PT</language>
        <sy:updatePeriod>hourly</sy:updatePeriod>
        <sy:updateFrequency>1</sy:updateFrequency>
        <generator>Empregos Yoyota</generator>

        <image>
            <url>{{ asset('storage/images/logo-full.jpg') }}</url>
            <title>Empregos Yoyota</title>
            <link>{{ url('/') }}</link>
            <width>32</width>
            <height>32</height>
        </image>

        @foreach($items as $item)
            <item>
                <title>{{ $item['title'] }}</title>
                <link>{{ $item['url'] }}</link>
                <dc:creator><![CDATA[Edivaldo Jorge]]></dc:creator>
                <pubDate>{{ $item['pubDate'] }}</pubDate>
                @foreach($item['categories'] as $cat)
                <category><![CDATA[{{ $cat }}]]></category>
                @endforeach
                <guid isPermaLink="false">{{ $item['guid'] }}</guid>
                <description><![CDATA[<p>{!! \Illuminate\Support\Str::limit(strip_tags($item['description']), 402, $end='...') !!}</p><p>O conteúdo <a href="{{ $item['url'] }}">{{ $item['title'] }}</a> aparece primeiro em <a href="{{ url('/') }}">Empregos Yoyota</a>.</p>
                ]]></description>
                <content:encoded><![CDATA[{!! $item['description'] !!}]]></content:encoded>
                <post-id xmlns="com-wordpress:feed-additions:1">{{ $item['guid'] }}</post-id>
            </item>
        @endforeach
    </channel>
</rss>
