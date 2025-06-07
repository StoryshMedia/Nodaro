<?php

namespace Smug\Core\Service\Base\Factory\Seo\Provider;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Components\Provider\DataProvider\TimeProvider;
use Smug\Core\Service\Base\Interfaces\Seo\SeoDataProviderInterface;
use DateTime;

/**
 * Class EventProvider
 * @package Smug\Core\Service\Base\Factory\Seo\Provider
 */
class EventProvider implements SeoDataProviderInterface
{
    /**
     * @inheritdoc
     */
    public static function provide(array $data, bool $list = false): array
    {
        $keywords = 'Storysh, Stories, Storish, Rezensionen, Tauschen, Bücher, Autoren, Benutzerbewertungen, Zusammenfassung, Diskussionen, Veranstaltungen, Events, Lesungen';
        
        if ($list === false) {
            $schemaFunction = 'getSchemaData';
            $keywords .= ', ' . $data['title'];
            $description = 'Storysh Event';

            if (DataHandler::getArrayLength($data['categories']) > 0) {
                foreach ($data['categories'] as $category) {
                    $description .= ' ' . $category['name'];
                    $keywords .=  ', ' . $category['name'];
                }
            }
            $image = $data['images'][0]['media']['src'];
            $description .= ' | ' . $data['teaser'];
            $title = 'Storysh | ' . $data['title'];
        } else {
            $schemaFunction = 'getListSchemaData';
            $title = 'Storysh | Events';
            $image = 'https://api.storysh.de/site/img/homeEventHeader.webp';
            $description = 'Entdecke eine vielfältige Liste fesselnder Literatur Events, die dich in die Welt der Worte entführen! Von literarischen Festivals, Lesungen und Buchmessen bis hin zu Autorenvorträgen und Schreibworkshops - unsere kuratierte Sammlung bietet dir ein facettenreiches Spektrum an Veranstaltungen für Leser und Autoren gleichermaßen. Tauche ein in die Inspiration, die durch die Begegnung mit renommierten Schriftstellern und gleichgesinnten Bücherliebhabern entsteht. Egal ob du nach neuen Leseerlebnissen suchst oder deine eigenen schriftstellerischen Fähigkeiten verbessern möchtest, diese Liste der Literatur Events ist dein Schlüssel zu einer Welt voller Geschichten und Ideen. Verpasse keine Gelegenheit, die Magie der Literatur hautnah zu erleben und tritt ein in eine aufregende Reise durch Worte und Emotionen!';
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

        $breadcrumb = 'Events > ';

        /** @var DateTime $date */
        $date = $data['eventDate'];

        if ($data['startTime'] !== '') {
            $arTimeData = DataHandler::explodeArray(':', $data['startTime']);
            $date->setTime(
                DataHandler::getIntFromString($arTimeData[0]),
                DataHandler::getIntFromString($arTimeData[1])
            );
        } else {
            $date->setTime(
                12,
                0
            );
        }

        $result['breadcrumb'] = $breadcrumb . $data['title'];
        $result['mainEntity'] = [
            '@context' => 'https://schema.org',
            '@type' => 'Event',
            'location' => [
                '@type' => 'Place',
                'address' => [
                    '@type' => 'PostalAddress',
                    'addressLocality' => $data['city'],
                    'postalCode' => $data['zipCode'],
                    'streetAddress' => $data['address']
                ]
            ],
            'name' => $data['title'],
            'offers' => [
                '@type' => 'Offer',
                'price' => ($data['ticketPrice'] === '') ? '0.00' : $data['ticketPrice'],
                'priceCurrency' => 'EUR',
                'url' => $data['url']
            ],
            'startDate' => $date->format(TimeProvider::ISO_8601),
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
            'description' => $description,
            'name' => 'Storysh Events',
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'Storysh'
            ]
        ];

        $breadcrumb = 'Events';

        $result['breadcrumb'] = $breadcrumb;

        return DataHandler::getJsonEncode($result);
    }
}
