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
        <atom:link href="{{ url('/feed') }}" rel="self" type="application/rss+xml" />
        <link>{{ url('/') }}</link>
        <description>Vagas de emprego, estágio e bolsas de estudo</description>
        <lastBuildDate>{{ date_format(new DateTime($jobs[0]['created_at']), DATE_ATOM) }}</lastBuildDate>
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

        @foreach($jobs as $job)
            @php
                $countryIdToCode = [1 => 'ao', 2 => 'br', 3 => 'mz'];
                $countryCode = $countryIdToCode[$job->country_id] ?? 'ao';

                $jobUrl = ($countryCode === 'ao')
                    ? url('/empregos/' . $job->slug)
                    : url('/' . $countryCode . '/empregos/' . $job->slug);
            @endphp
            <item>
                <title>{{$job->title}}</title>
                <link>{{ $jobUrl }}</link>
                <dc:creator><![CDATA[Edivaldo Jorge]]></dc:creator>
                <pubDate>{{ date_format(new DateTime($job['created_at']), DATE_ATOM) }}</pubDate>
                <category><![CDATA[Emprego]]></category>
                <category><![CDATA[Estágio]]></category>
                <guid isPermaLink="false">{{ $jobUrl }}</guid>
                <description><![CDATA[<p>{!! \Illuminate\Support\Str::limit(strip_tags($job->description), 402, $end='...') !!}</p><p>O conteúdo <a href="{{ $jobUrl }}">{{$job->title}}</a> aparece primeiro em <a href="{{ url('/') }}">Empregos Yoyota</a>.</p>
                ]]></description>
                <content:encoded><![CDATA[{{$job->description}}]]></content:encoded>
                <post-id xmlns="com-wordpress:feed-additions:1">{{$job->id}}</post-id>
            </item>
        @endforeach
    </channel>
</rss>
