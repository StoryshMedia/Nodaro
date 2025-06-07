<?php

namespace Smug\Core\Service\Base\Factory\Seo\Provider;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Components\LanguageDetector\CheckLanguage;
use Smug\Core\Service\Base\Components\LanguageDetector\LanguageDetector;
use Smug\Core\Service\Base\Interfaces\Seo\SeoDataProviderInterface;

/**
 * Class PublicationProvider
 * @package Smug\Core\Service\Base\Factory\Seo\Provider
 */
class PublicationProvider implements SeoDataProviderInterface
{
    /**
     * @inheritdoc
     */
    public static function provide(array $data, bool $list = false): array
    {
        $image = '';
        $keywords = 'Storysh, Stories, Storish, Rezensionen, Tauschen, Bücher, Autoren, Benutzerbewertungen, Zusammenfassung, Diskussionen';
        
        if ($list === false) {
            $schemaFunction = 'getSchemaData';
            $keywords .= ', '. $data['completeName'];

            $description = $data['title'];
            $title = 'Storysh | ' . $data['title'];

            if (!DataHandler::isEmpty($data['subTitle'])) {
                $description .= ' - ' . $data['subTitle'];
                $title .= ' - ' . $data['subTitle'];
            }

            $description .= ': Autor(en): ';
            $lastAuthor = DataHandler::getLastArrayElement($data['authors']);

            foreach ($data['authors'] as $author) {
                $description .= $author['firstName'] . ' ' . $author['lastName'];
                $keywords .= $author['completeName'];

                if ($lastAuthor['slug'] !== $author['slug']) {
                    $description .= ', ';
                }
            }

            $description .= ' ' . $data['teaser'];

            if (DataHandler::doesKeyExists('images', $data) && DataHandler::isArray($data['images'])) {
                if (!DataHandler::isEmpty($data['images'])) {
                    $image = $data['images'][0]['media']['src'];
                }
            }
        
            return [
                'description' => $description,
                'title' => $title,
                'image' => $image,
                'schema' => self::$schemaFunction($data, $description),
                'keywords' => trim(preg_replace('/\s+/', ' ', $keywords))
            ];
        } else {
            $schemaFunction = 'getListSchemaData';
            $image = 'https://api.storysh.de/site/img/homePublicationHeader.webp';
            $title = 'Storysh | Publikationen';
            $description = 'Stöbere durch unsere Bücherliste und entdecke ein Universum fesselnder Geschichten! Auf unserer Webseite haben wir sorgfältig eine Sammlung von Meisterwerken, zeitlosen Klassikern und aufstrebenden Autoren zusammengestellt, die dich verzaubern werden. Tauche ein in verschiedene Genres, reise durch ferne Welten, erlebe die Fülle menschlicher Emotionen und finde neue Schätze für deine Leseliste. Egal, ob du nach inspirierenden Abenteuern, tiefgründigen Romanen oder spannenden Thrillern suchst - unsere Bücherliste bietet für jeden Geschmack etwas Besonderes. Bereite dich darauf vor, in die faszinierende Welt der Bücher einzutauchen und deiner Leselust freien Lauf zu lassen. Verpasse nicht die Gelegenheit, dich von der Kraft der Worte und der Magie der Literatur begeistern zu lassen. Lass dich inspirieren und finde dein nächstes literarisches Abenteuer auf unserer einzigartigen Bücherliste!';
        
            return [
                'description' => $description,
                'title' => $title,
                'image' => $image,
                'canonical' => 'https://storysh.de/publications',
                'schema' => self::$schemaFunction($data, $description),
                'keywords' => trim(preg_replace('/\s+/', ' ', $keywords))
            ];
        }
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

        $breadcrumb = 'Startseite > Publikationen > ';

        if (DataHandler::getArrayLength($data['genres']) > 0) {
            foreach ($data['genres'] as $genre) {
                $breadcrumb .= $genre['title'] .= ' > ';
            }
        }

        $languages = LanguageDetector::detect(
            $data['summary'],
            CheckLanguage::detectLanguages
        )->getScores();

        $detectedLanguage = DataHandler::getFirstArrayKey($languages);

        $result['breadcrumb'] = $breadcrumb . $data['completeName'];
        $result['mainEntity'] = [
            '@type' => 'Book',
            'bookFormat' => 'https://schema.org/Paperback',
            'numberOfPages' => $data['pageCount'],
            'datePublished' => $data['publishDate'],
            'description' => $description,
            'name' => $data['completeName'],
            'isbn' => (DataHandler::isEmpty($data['isbnLong'])) ? $data['isbn'] : $data['isbnLong'],
            'image' => (DataHandler::isEmpty($data['images'])) ? $data['image'] : $data['images'][0]['media']['src']
        ];

        if (DataHandler::doesKeyExists('title', $data['publisher'])) {
            $result['mainEntity']['publisher'] = [
                '@type' => 'Organization',
                'name' => $data['publisher']['title']
            ];
        }

        if (!DataHandler::isEmpty($detectedLanguage) && DataHandler::isInArray($detectedLanguage, CheckLanguage::languageStructuredDataLabels)) {
            $result['mainEntity']['inLanguage'] = CheckLanguage::languageStructuredDataLabels[$detectedLanguage];
        }

        if (DataHandler::getArrayLength($data['genres']) > 0) {
            $result['mainEntity']['genre'] = $data['genres'][0]['title'];
        }
        
        if (DataHandler::getArrayLength($data['prices']) > 0) {
            $offer = [];
            foreach ($data['prices'] as $price) {
                if ($price['territory'] === 'DE') {
                    $offer = [
                        '@type' => 'Offer',
                        'availability' => 'https://schema.org/InStock',
                        'price'=> $price['price'],
                        'priceCurrency'=> $price['currency']
                    ];
                }
            }

            if (DataHandler::isEmpty($offer)) {
                $offer = [
                    '@type' => 'Offer',
                    'availability' => 'https://schema.org/InStock',
                    'price'=> $data['prices'][0]['price'],
                    'priceCurrency'=> $data['prices'][0]['currency']
                ];
            }

            $result['mainEntity']['offers'] = $offer;
        }

        $authors = [];
        foreach ($data['authors'] as $author) {
            $authors[] = [
                [
                  '@type' => 'Person',
                  'url' => 'https://storysh.de/authors/' . $author['slug'],
                  'name' => $author['completeName']
                ]
            ];
        }

        $result['mainEntity']['author'] = $authors;

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
            'name' => 'Storysh Publikationen',
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'Storysh'
            ]
        ];

        $breadcrumb = 'Publikationen';

        $result['breadcrumb'] = $breadcrumb;

        return DataHandler::getJsonEncode($result);
    }
}
