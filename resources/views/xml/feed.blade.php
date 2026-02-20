<?xml version="1.0" encoding="UTF-8"?>
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
        <atom:link href="https://ao.empregosyoyota.net/feed/" rel="self" type="application/rss+xml" />
        <link>https://ao.empregosyoyota.net/</link>
        <description>Vagas de emprego, estágio e bolsas de estudo</description>
        <lastBuildDate>{{ date_format(new DateTime($jobs[0]['created_at']), DATE_ATOM) }}</lastBuildDate>
        <language>pt-PT</language>
        <sy:updatePeriod>hourly</sy:updatePeriod>
        <sy:updateFrequency>1</sy:updateFrequency>
        <generator>https://wordpress.org/?v=6.4.2</generator>

        <image>
            <url>https://ao.empregosyoyota.net/storage/images/logo-full.jpg</url>
            <title>Empregos Yoyota</title>
            <link>https://ao.empregosyoyota.net/</link>
            <width>32</width>
            <height>32</height>
        </image>

        <site xmlns="com-wordpress:feed-additions:1">227777157</site>

        @foreach($jobs as $job)
            <item>
                <title>{{$job->title}}</title>
                <link>https://ao.empregosyoyota.net/empregos/{{$job->slug}}</link>
                <dc:creator><![CDATA[Edivaldo Jorge]]></dc:creator>
                <pubDate>{{ date_format(new DateTime($job['created_at']), DATE_ATOM) }}</pubDate>
                <category><![CDATA[Emprego]]></category>
                <category><![CDATA[Estágio]]></category>
                <guid isPermaLink="false">https://ao.empregosyoyota.net/empregos/{{$job->slug}}</guid>
                <description><![CDATA[<p>{!! \Illuminate\Support\Str::limit(strip_tags($job->description), 402, $end='...') !!}</p><p>O conteúdo <a href="https://ao.empregosyoyota.net/empregos/{{$job->slug}}">{{$job->title}}</a> aparece primeiro em <a href="https://ao.empregosyoyota.net">Empregos Yoyota</a>.</p>
                ]]></description>
                <content:encoded><![CDATA[{{$job->description}}]]></content:encoded>
                <post-id xmlns="com-wordpress:feed-additions:1">{{$job->id}}</post-id>
            </item>
        @endforeach
    </channel>
</rss>
