<?php

namespace Smug\Core\Service\Base\Factory\Seo\Provider;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Interfaces\Seo\SeoDataProviderInterface;

/**
 * Class ContestProvider
 * @package Smug\Core\Service\Base\Factory\Seo\Provider
 */
class ContestProvider implements SeoDataProviderInterface
{
    /**
     * @inheritdoc
     */
    public static function provide(array $data, bool $list = false): array
    {
        $image = '';
        $keywords = 'Storysh, Stories, Storish, Rezensionen, Tauschen, Bücher, Autoren, Benutzerbewertungen, Zusammenfassung, Diskussionen, Blog, Schreibwettbewerbe, Wettbewerb';

        if ($list === false) {
            $schemaFunction = 'getSchemaData';
            $keywords .= ', ' . $data['title'] . ', ' . $data['topic'];
            $image = $data['images'][0]['media']['src'];
            $description = $data['topic'] . ' ' . $data['title'];
            $title = 'Storysh | Schreibwettbewerbe ' . $data['topic'] . ' ' . $data['title'];

            $description .= ': ' . $data['description'];
        } else {
            $schemaFunction = 'getListSchemaData';
            $title = 'Storysh | Wettbewerbe';
            $image = 'https://api.storysh.de/site/img/contestTeaser.webp';
            $description = 'Entfache die Schreibbegeisterung an Deiner Schule mit unseren fesselnden Schreibwettbewerben! Lass Schülerinnen und Schüler in die Welt der Worte eintauchen, ihre Kreativität entfesseln und ihre Geschichten zum Leben erwecken. Unsere Schreibwettbewerbe bieten eine einzigartige Plattform, um junge Talente zu fördern, ihre literarischen Fähigkeiten zu schärfen und ihre Werke einer breiteren Öffentlichkeit zu präsentieren. Schaffe eine inspirierende Atmosphäre des Schreibens, Wettbewerbs und der Gemeinschaft an deiner Schule. Entdecke jetzt unsere vielfältigen Wettbewerbsmöglichkeiten und öffne die Türen zu einer Welt voller kreativer Entfaltung und literarischer Erfolge!';
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
        $breadcrumb = 'Schreibwettbewerbe > ';

        $result = [
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            '@type' => 'BlogPosting',
            '@id' => 'https://storysh.de/wettbewerbe/schule/wettbewerb/' . $data['slug'],
            'url' => 'https://storysh.de/wettbewerbe/schule/wettbewerb/' . $data['slug'],
            'mainEntityOfPage' => 'https://storysh.de/wettbewerbe',
            'breadcrumb' => $breadcrumb . $data['title'],
            'headline' => $data['title'],
            'name' => $data['title'],
            'description' => $data['description'],
            "publisher" => [
                "@type" => "Organization",
                "@id" => "https://storysh.de",
                "name" => "Storysh",
                "logo" => [
                    "@type" => "ImageObject",
                    "@id" => "https://api.storysh.de/site/img/EmailLogo.png",
                    "url" => "https://api.storysh.de/site/img/EmailLogo.png"
                ]
            ],
            "image" => [
                "@type" =>  "ImageObject",
                "@id" =>  $data['images'][0]['media']['src'],
                "url" =>  $data['images'][0]['media']['src']
            ],
            "url" => "https://storysh.de/wettbewerbe/schule/wettbewerb/" . $data['slug'],
            "isPartOf" => [
                "@type" => "Blog",
                "@id" => "https://storysh.de/wettbewerbe",
                "name" => "Storysh Schreibwettbewerbe",
                "publisher" => [
                    "@type" => "Organization",
                    "@id" => "https://storysh.de",
                    "name" => "Storysh"
                ]
            ],
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
            'description' => $description,
            'name' => 'Storysh Wettbewerbe',
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'Storysh'
            ]
        ];

        $breadcrumb = 'Schreibwettbewerbe';

        $result['breadcrumb'] = $breadcrumb;

        return DataHandler::getJsonEncode($result);
    }
}
