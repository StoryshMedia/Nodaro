<?php

namespace Smug\Core\Service\Base\Factory\Seo\Provider;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Interfaces\Seo\SeoDataProviderInterface;

/**
 * Class QuoteProvider
 * @package Smug\Core\Service\Base\Factory\Seo\Provider
 */
class QuoteProvider implements SeoDataProviderInterface
{
    /**
     * @inheritdoc
     */
    public static function provide(array $data, bool $list = false): array
    {
        $image = $data['image'];
        $keywords = 'Storysh, Stories, Storish, Rezensionen, Tauschen, Bücher, Autoren, Benutzerbewertungen, Zusammenfassung, Diskussionen, Zitate, Zitat des Tages';
        
        if ($list === false) {
            $keywords .=  ' ' . $data['quoteDateString'];
            $schemaFunction = 'getSchemaData';
            $description = $data['title'];
            $title = 'Storysh | ' . $data['title'];
    
            $description .= ': Verfasser: ' . $data['information'];
        } else {
            $schemaFunction = 'getListSchemaData';
            $title = 'Storysh | Zitate';
            $description = 'Storysh listet eine Reihe von literarischen Zitaten auf, die dem User zum Nachdenken anregen sollen, oder auch einfach nur ein Schmunzeln ins Gesicht zaubern sollen.';
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

        $breadcrumb = 'Zitate > ';

        $result['breadcrumb'] = $breadcrumb . $data['title'];
        $result['mainEntity'] = [
            '@type' => 'Quotation',
            'text' => $data['title'],
            'creator' => $data['information'],
            'image' => 'https://storysh.de/site/img/quoteTeaserImage.webp'
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
            'image' => 'https://storysh.de/site/img/quoteTeaserImage.webp',
            'name' => 'Storysh | Zitate',
            'description' => $description
        ];

        $breadcrumb = 'Zitate';

        $result['breadcrumb'] = $breadcrumb;

        return DataHandler::getJsonEncode($result);
    }
}
