<?php

namespace App\Http\Controllers;

use App\Models\CaseStudy;
use App\Models\Profile;
use Illuminate\Http\Response;
use XMLWriter;

/**
 * Crawler-facing files, generated rather than static so every URL follows the current
 * host: moving to a custom domain changes APP_URL and nothing here.
 */
class SeoController extends Controller
{
    public function sitemap(): Response
    {
        $studies = CaseStudy::published()->ordered()->get();

        // The home page shows the profile and every study, so it changed when any of them did.
        $homeUpdatedAt = collect([Profile::current()->updated_at, $studies->max('updated_at')])->filter()->max();

        $xml = new XMLWriter;
        $xml->openMemory();
        $xml->startDocument('1.0', 'UTF-8');
        $xml->startElement('urlset');
        $xml->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

        $this->url($xml, url('/'), $homeUpdatedAt?->toAtomString());

        foreach ($studies as $study) {
            $this->url($xml, route('projects.show', $study), $study->updated_at?->toAtomString());
        }

        $xml->endElement();
        $xml->endDocument();

        return response($xml->outputMemory(), 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    public function robots(): Response
    {
        // /admin is a login page: nothing there belongs in search results.
        $body = implode("\n", [
            'User-agent: *',
            'Disallow: /admin',
            '',
            'Sitemap: '.url('/sitemap.xml'),
            '',
        ]);

        return response($body, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    private function url(XMLWriter $xml, string $loc, ?string $lastModified): void
    {
        $xml->startElement('url');
        $xml->writeElement('loc', $loc);

        if ($lastModified !== null) {
            $xml->writeElement('lastmod', $lastModified);
        }

        $xml->endElement();
    }
}
