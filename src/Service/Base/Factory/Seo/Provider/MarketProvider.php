<?php

namespace Smug\Core\Service\Base\Factory\Seo\Provider;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Interfaces\Seo\SeoDataProviderInterface;
use DateTime;

/**
 * Class MarketProvider
 * @package Smug\Core\Service\Base\Factory\Seo\Provider
 */
class MarketProvider implements SeoDataProviderInterface
{
    /**
     * @inheritdoc
     */
    public static function provide(array $data, bool $list = false): array
    {
        $image = '';
        $keywords = 'Storysh, Stories, Storish, Rezensionen, Tauschen, Bücher, Autoren, Benutzerbewertungen, Zusammenfassung, Diskussionen, Veranstaltungen, Events, Lesungen';
        
        if ($list === false) {
            $schemaFunction = 'getSchemaData';
            $keywords .= ', ' . $data['title'];
            $description = 'Storysh Event';
            $image = $data['images'][0]['media']['src'];
            $description .= ' | ' . $data['description'];
            $title = 'Storysh Taschbörse | ' . $data['title'];
        } else {
            $schemaFunction = 'getListSchemaData';
            $title = 'Storyh | Taschbörse';
            $description = 'Willkommen in unserer lebendigen Büchertauschbörse - dem Paradies für Bücherliebhaber! Entdecke eine Schatztruhe voller Geschichten, in der du deine gelesenen Schätze gegen neue Abenteuer eintauschen kannst. Tritt unserer begeisterten Gemeinschaft bei, um literarische Schätze zu entdecken und deine eigenen Bücher einem neuen Leser zukommen zu lassen. Hier kannst du nicht nur Platz in deinem Bücherregal schaffen, sondern auch besondere literarische Entdeckungen machen. Teile deine Leseeindrücke, tausche Empfehlungen aus und finde Gleichgesinnte, die deine Leidenschaft für Bücher teilen. Ob Bestseller, Klassiker oder Geheimtipps - unsere Büchertauschbörse bietet eine bunte Auswahl an Genres und Autoren. Verpasse nicht die Gelegenheit, deine Buchsammlung zu erweitern und neue literarische Welten zu erkunden. Tauche ein in eine einzigartige Gemeinschaft, die die Liebe zur Literatur feiert und den Zauber des Lesens in vollen Zügen genießt!';
            $image = 'https://api.storysh.de/site/img/homeMarketHeader.webp';
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

        $breadcrumb = 'Tauschbörse > ';


        $result['breadcrumb'] = $breadcrumb . $data['title'];
        $result['mainEntity'] = [
            '@context' => 'https://schema.org',
            '@type' => 'TradeAction',
            'agent' => [
                '@type' => 'Person',
                'name' => $data['user']['name']
            ],
            'object' => [
                '@type' => 'Book',
                'name' => $data['publications'][0]['completeName']
            ],
            'price' => '0.00',
            'priceCurrency' => 'EUR'
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
            'name' => 'Storysh Tauschbörse',
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'Storysh'
            ]
        ];

        $breadcrumb = 'Tauschbörse';

        $result['breadcrumb'] = $breadcrumb;

        return DataHandler::getJsonEncode($result);
    }
}
