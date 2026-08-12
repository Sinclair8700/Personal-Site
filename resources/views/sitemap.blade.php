<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ url('/') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc>{{ url('/projects') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc>{{ url('/education') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    <url>
        <loc>{{ url('/contact') }}</loc>
        <changefreq>yearly</changefreq>
        <priority>0.5</priority>
    </url>

    @foreach ($projects as $project)
    <url>
        <loc>{{ url('/projects/' . $project->slug) }}</loc>
        <lastmod>{{ $project->updated_at->toAtomString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
    @endforeach

    @foreach ($education as $item)
    <url>
        <loc>{{ url('/education/' . $item->slug) }}</loc>
        <lastmod>{{ $item->updated_at->toAtomString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
    @endforeach
</urlset>
