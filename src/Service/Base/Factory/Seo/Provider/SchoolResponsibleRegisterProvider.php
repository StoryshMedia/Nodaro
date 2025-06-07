<?php

namespace Smug\Core\Service\Base\Factory\Seo\Provider;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Interfaces\Seo\SeoDataProviderInterface;

/**
 * Class SchoolResponsibleRegisterProvider
 * @package Smug\Core\Service\Base\Factory\Seo\Provider
 */
class SchoolResponsibleRegisterProvider implements SeoDataProviderInterface
{
    /**
     * @inheritdoc
     */
    public static function provide(array $data, bool $list = false): array
    {
        $keywords = 'Storysh, Stories, Storish, Rezensionen, Tauschen, Bücher, Autoren, Benutzerbewertungen, Zusammenfassung, Diskussionen, Blog, Schreibwettbewerbe, Wettbewerb';

        $schemaFunction = 'getListSchemaData';
        $title = 'Storysh | Registration Wettbewerbe';
        $image = 'https://api.storysh.de/site/img/contestTeaser.webp';
        $description = 'Entfache die Schreibbegeisterung an deiner Schule mit unseren fesselnden Schreibwettbewerben! Lass Schülerinnen und Schüler in die Welt der Worte eintauchen, ihre Kreativität entfesseln und ihre Geschichten zum Leben erwecken. Unsere Schreibwettbewerbe bieten eine einzigartige Plattform, um junge Talente zu fördern, ihre literarischen Fähigkeiten zu schärfen und ihre Werke einer breiteren Öffentlichkeit zu präsentieren. Schaffe eine inspirierende Atmosphäre des Schreibens, Wettbewerbs und der Gemeinschaft an deiner Schule. Entdecke jetzt unsere vielfältigen Wettbewerbsmöglichkeiten und öffne die Türen zu einer Welt voller kreativer Entfaltung und literarischer Erfolge!';

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
        return DataHandler::getJsonEncode([]);
    }

    /**
     * @inheritdoc
     */
    public static function getListSchemaData(array $data, string $description): string
    {
        return DataHandler::getJsonEncode([]);
    }
}
