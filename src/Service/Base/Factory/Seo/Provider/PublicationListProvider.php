<?php

namespace Smug\Core\Service\Base\Factory\Seo\Provider;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Interfaces\Seo\SeoDataProviderInterface;

/**
 * Class PublicationListProvider
 * @package Smug\Core\Service\Base\Factory\Seo\Provider
 */
class PublicationListProvider implements SeoDataProviderInterface
{
    /**
     * @inheritdoc
     */
    public static function provide(array $data, bool $list = false): array
    {
        $keywords = 'Storysh, Stories, Storish, Rezensionen, Tauschen, Bücher, Autoren, Benutzerbewertungen, Zusammenfassung, Diskussionen, Genres, Empfehlungen';
        
        if ($list === false) {
            $keywords .= ', ' . $data['title'];
            $schemaFunction = 'getSchemaData';
            $description = 'Storysh Empfehlungen';

            $description .= ' | ' . $data['title'];
            $title = 'Storysh | ' . $data['title'];
            $image = $data['images'][0]['media']['src'];
        } else {
            $schemaFunction = 'getListSchemaData';
            $title = 'Storysh | Empfehlungen';
            $description = 'Unsere Buchempfehlungen aufgegliedert nach Themengebieten. Für jeden Geschmack haben wir eine kleine Auswahl zusammengestellt, und hoffen, dass wir auch Deinen Geschmack getroffen haben.';
            $image = 'https://api.storysh.de/site/img/homeGenreHeader.webp';
        }

        return [
            'description' => $description,
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

        $breadcrumb = 'Empfehlungen > ';

        $result['breadcrumb'] = $breadcrumb . $data['title'];
        $result['mainEntity'] = [
            '@context' => 'https://schema.org',
            '@type' => 'Genre',
            '@id' => 'https://storysh.de/empfehlungen/' . $data['slug'],
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
            '@id' => 'https://storysh.de/empfehlungen',
            'name' => 'Storysh | Empfehlungen',
            'description' => $description
        ];

        $breadcrumb = 'Empfehlungen';

        $result['breadcrumb'] = $breadcrumb;

        return DataHandler::getJsonEncode($result);
    }
}
