<?php

namespace App\Support;

/**
 * The SEO and link-preview data for one page.
 *
 * It travels as the `meta` Inertia prop, so a single value feeds both consumers:
 * the Blade root view prints it into the server HTML (link-preview bots and search
 * crawlers never run JavaScript), and Vue's <Head> keeps the tab title in step during
 * client-side navigation.
 */
final class PageMeta
{
    /** Social cards render at 1.91:1; 1200×630 is the size every major platform accepts. */
    public const IMAGE_WIDTH = 1200;

    public const IMAGE_HEIGHT = 630;

    /**
     * @param  array<string, mixed>|null  $jsonLd  schema.org data, printed as application/ld+json
     * @return array{title: string, description: string, url: string, type: string, siteName: string, image: string, imageAlt: string, imageWidth: int, imageHeight: int, jsonLd: array<string, mixed>|null}
     */
    public static function make(
        string $title,
        string $description,
        string $siteName,
        string $image,
        string $imageAlt,
        string $type = 'website',
        ?array $jsonLd = null,
    ): array {
        return [
            'title' => $title,
            'description' => $description,
            // Canonical URL without the query string, so tracking parameters never split the page.
            'url' => url()->current(),
            'type' => $type,
            'siteName' => $siteName,
            'image' => $image,
            'imageAlt' => $imageAlt,
            'imageWidth' => self::IMAGE_WIDTH,
            'imageHeight' => self::IMAGE_HEIGHT,
            'jsonLd' => $jsonLd,
        ];
    }

    /** A page-specific card if one has been rendered, otherwise the site-wide card. */
    public static function image(?string $specific = null): string
    {
        if ($specific !== null && is_file(public_path($specific))) {
            return asset($specific);
        }

        return asset('og/portfolio.png');
    }
}
