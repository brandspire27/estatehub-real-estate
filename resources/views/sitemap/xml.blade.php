{!! '<'.'?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">

    <!-- Core Public Landing Pages -->
    @foreach($staticPages as $page)
        <url>
            <loc>{{ $page['loc'] }}</loc>
            <lastmod>{{ $page['lastmod'] }}</lastmod>
            <changefreq>{{ $page['changefreq'] }}</changefreq>
            <priority>{{ $page['priority'] }}</priority>
        </url>
    @endforeach

    <!-- City Specific Discovery Pages -->
    @foreach($cityPages as $page)
        <url>
            <loc>{{ $page['loc'] }}</loc>
            <lastmod>{{ $page['lastmod'] }}</lastmod>
            <changefreq>{{ $page['changefreq'] }}</changefreq>
            <priority>{{ $page['priority'] }}</priority>
        </url>
    @endforeach

    <!-- Published Real Estate Inventory -->
    @foreach($properties as $prop)
        <url>
            <loc>{{ $prop['loc'] }}</loc>
            <lastmod>{{ $prop['lastmod'] }}</lastmod>
            <changefreq>{{ $prop['changefreq'] }}</changefreq>
            <priority>{{ $prop['priority'] }}</priority>
            @if(!empty($prop['image']))
                <image:image>
                    <image:loc>{{ $prop['image'] }}</image:loc>
                    <image:title>{{ htmlspecialchars($prop['title'], ENT_XML1, 'UTF-8') }}</image:title>
                </image:image>
            @endif
        </url>
    @endforeach

</urlset>
