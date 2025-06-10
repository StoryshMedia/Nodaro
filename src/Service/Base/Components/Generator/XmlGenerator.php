<?php

namespace Smug\Core\Service\Base\Components\Generator;

use Smug\Core\Service\Base\Components\Handler\TimeHandler;
use Smug\Core\Service\Base\Components\Provider\DataProvider\TimeProvider;

class XmlGenerator
{
    /**
     * returns the header for the sitemap 
     * @return string
     * */
    public static function getSitemapIndexHeader(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>
        <sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    }

    /**
     * returns the header for the sitemap 
     * @return string
     * */
    public static function getSitemapHeader(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    }

    /**
     * returns the url component for a sitemap entry 
     * @param string $slug
     * @return string
     * */
    public static function getSitemapUrl(string $slug): string
    {
        $url = '<url>';

        $url .= '<loc>' . $slug . '</loc>';
        $url .= '<lastmod>' . TimeHandler::getNewDateObject()->format(TimeProvider::DATE_OUTPUT_FORMAT_INT) . '</lastmod>';

        $url .= '</url>';
        
        return $url;
    }

    /**
     * returns the url component for a sitemap index entry 
     * @param string $slug
     * @return string
     * */
    public static function getSitemapIndexUrl(string $slug): string
    {
        $url = '<sitemap>';

        $url .= '<loc>' . $slug . '</loc>';

        $url .= '</sitemap>';
        
        return $url;
    }

    /**
     * returns the footer for the sitemap 
     * @return string
     * */
    public static function getSitemapFooter(): string
    {
        return '</urlset>';
    }

    /**
     * returns the footer for the sitemap 
     * @return string
     * */
    public static function getSitemapIndexFooter(): string
    {
        return '</sitemapindex>';
    }
}
