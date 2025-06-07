<?php

namespace Smug\Core\Service\Base\Factory\Seo\Provider;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Components\LanguageDetector\CheckLanguage;
use Smug\Core\Service\Base\Components\LanguageDetector\LanguageDetector;
use Smug\Core\Service\Base\Interfaces\Seo\SeoDataProviderInterface;

/**
 * Class StoryProvider
 * @package Smug\Core\Service\Base\Factory\Seo\Provider
 */
class StoryProvider implements SeoDataProviderInterface
{
    /**
     * @inheritdoc
     */
    public static function provide(array $data, bool $list = false): array
    {
        $image = '';
        $keywords = 'Storysh, Stories, Storish, Rezensionen, Tauschen, Bücher, Autoren, Benutzerbewertungen, Zusammenfassung, Diskussionen, Selbstverlegen, Self-Publishing';
        
        if ($list === false) {
            $schemaFunction = 'getSchemaData';
            $description = $data['completeName'];
            $keywords .= ', ' . $data['completeName'];
            $title = 'Storysh | User Stories | ' . $data['completeName'];
    
            $description .= ': Autor: ' . $data['author']['name'];
            $description .= ' ' . DataHandler::truncateString($data['chapters'][0]['text'], 144);
    
            if (DataHandler::doesKeyExists('images', $data) && DataHandler::isArray($data['images'])) {
                if (!DataHandler::isEmpty($data['images'])) {
                    $image = $data['images'][0]['media']['src'];
                }
            }

        }

        if ($list === true) {
            $schemaFunction = 'getListSchemaData';
            $title = 'Storysh | User Stories';
            $image = 'https://api.storysh.de/site/img/homeStoryHeader.webp';
            $description = 'Storysh | Schreibe Deine eigenen Geschichten, teile sie mit anderen Usern und lasse sie an Deiner Fantasie teilhaben.';
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

        $breadcrumb = 'User Stories > ';

        if (DataHandler::getArrayLength($data['genres']) > 0) {
            foreach ($data['genres'] as $genre) {
                $breadcrumb .= $genre['title'] .= ' > ';
            }
        }

        $languages = LanguageDetector::detect(
            $data['chapters'][0]['text'],
            CheckLanguage::detectLanguages
        )->getScores();

        $detectedLanguage = DataHandler::getFirstArrayKey($languages);

        $result['breadcrumb'] = $breadcrumb . $data['completeName'];
        $result['mainEntity'] = [
            '@type' => 'Book',
            'bookFormat' => 'https://schema.org/CreativeWork',
            'datePublished' => $data['publishDate'],
            'description' => DataHandler::truncateString($data['chapters'][0]['text'], 144),
            'name' => $data['completeName'],
            'author' => [
                'type' => 'https://schema.org/Person',
                'name' => $data['author']['name'],
                'id' => 'https://storysh.de/community/' . $data['author']['slug']
            ],
            'image' => (DataHandler::isEmpty($data['images'])) ? $data['image'] : $data['images'][0]['media']['src']
        ];

        if (!DataHandler::isEmpty($detectedLanguage)) {
            $result['mainEntity']['inLanguage'] = CheckLanguage::languageStructuredDataLabels[$detectedLanguage];
        }

        if (DataHandler::getArrayLength($data['genres']) > 0) {
            $result['mainEntity']['genre'] = $data['genres'][0]['title'];
        }

        if (DataHandler::getArrayLength($data['reviews']) > 0) {
            $result['mainEntity']['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => $data['rating'],
                'reviewCount' => DataHandler::getArrayLength($data['reviews']),
            ];
        }

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
            'description' => $description,
            'name' => 'Storysh User Stories',
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'Storysh'
            ]
        ];

        $breadcrumb = 'User Stories';

        $result['breadcrumb'] = $breadcrumb;

        return DataHandler::getJsonEncode($result);
    }
}
