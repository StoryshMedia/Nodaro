<?php

namespace Smug\Core\Service\Base\Factory\Seo\Provider;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Interfaces\Seo\SeoDataProviderInterface;

/**
 * Class GenreProvider
 * @package Smug\Core\Service\Base\Factory\Seo\Provider
 */
class GenreProvider implements SeoDataProviderInterface
{
    /**
     * @inheritdoc
     */
    public static function provide(array $data, bool $list = false): array
    {
        $keywords = 'Storysh, Stories, Storish, Rezensionen, Tauschen, Bücher, Autoren, Benutzerbewertungen, Zusammenfassung, Diskussionen, Genres';
        
        $canonical = 'https://storysh.de/publications';

        if ($list === false) {
            $keywords .= ', ' . $data['title'];
            $schemaFunction = 'getSchemaData';
            $description = 'Storysh Genres';

            $description .= ' | ' . $data['description'];
            $title = 'Storysh | ' . $data['title'];
            $image = $data['images'][0]['media']['src'];
            $canonical = 'https://storysh.de/genres/' . $data['slug'];
        } else {
            $schemaFunction = 'getListSchemaData';
            $title = 'Storysh | Genres';
            $description = 'Alle Genres auf einem Blick zusammengefasst. Stöbere durch alle Genres und finde die für Dich passenden Publikationen und User Stories.';
            $image = 'https://api.storysh.de/site/img/homeGenreHeader.webp';
        }

        return [
            'description' => $description,
            'canonical' => $canonical,
            'title' => $title,
            'image' => $image,
            'schema' => self::$schemaFunction($data, $description),
            'keywords' => trim(preg_replace('/\s+/', ' ', $keywords))
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getSchemaData(array $data, string $description): string
    {
        $result = [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage'
        ];

        $breadcrumb = 'Genres > ';

        $result['breadcrumb'] = $breadcrumb . $data['title'];
        $result['mainEntity'] = [
            '@context' => 'https://schema.org',
            '@type' => 'Genre',
            '@id' => 'https://storysh.de/genres/' . $data['slug'],
            'name' => $data['title'],
            'image' => $data['images'][0]['media']['src']
        ];

        return DataHandler::getJsonEncode($result);
    }

    /**
     * @inheritdoc
     */
    public static function getListSchemaData(array $data, string $description): string
    {
        $result = [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            '@id' => 'https://storysh.de/genres',
            'name' => 'Storysh | Genres',
            'description' => $description
        ];

        $breadcrumb = 'Genres';

        $result['breadcrumb'] = $breadcrumb;

        return DataHandler::getJsonEncode($result);
    }
}
