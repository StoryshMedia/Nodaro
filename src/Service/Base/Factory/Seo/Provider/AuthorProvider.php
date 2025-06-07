<?php

namespace Smug\Core\Service\Base\Factory\Seo\Provider;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Interfaces\Seo\SeoDataProviderInterface;

/**
 * Class AuthorProvider
 * @package Smug\Core\Service\Base\Factory\Seo\Provider
 */
class AuthorProvider implements SeoDataProviderInterface
{
    /**
     * @inheritdoc
     */
    public static function provide(array $data, bool $list = false): array
    {
        $image = '';

        $keywords = 'Storysh, Stories, Storish, Rezensionen, Tauschen, Bücher, Autoren, Benutzerbewertungen, Zusammenfassung, Diskussionen';

        if ($list === false) {
            $keywords .= ', '. $data['completeName'];
            $description = 'Storysh | ' . $data['biography'];
            $title = 'Storysh | ' . $data['completeName'];

            if (DataHandler::doesKeyExists('images', $data) && DataHandler::isArray($data['images'])) {
                if (!DataHandler::isEmpty($data['images'])) {
                    $image = $data['images'][0]['media']['src'];
                }
            }

            return [
                'description' => $description,
                'title' => $title,
                'image' => $image,
                'schema' => self::getSchemaData($data, $description),
                'keywords' => trim(preg_replace('/\s+/', ' ', $keywords))
            ];
        } else {
            $title = 'Storysh | Autoren';
            $description = 'Storysh | Erkunde unsere talentierte Autorenliste und lass dich von der kreativen Vielfalt begeistern! Unsere Webseite präsentiert eine erlesene Auswahl an Schriftstellern, die mit ihren Werken fesseln, berühren und inspirieren. Von etablierten Bestsellerautoren bis hin zu aufstrebenden Geistesgrößen - entdecke die einzigartigen Stimmen, die die Magie der Literatur zum Leben erwecken. Tauche ein in eine Welt voller Fantasie, Romantik, Spannung und Tiefgründigkeit, während du die Werke dieser Schreibkünstler erkundest. Egal, ob du nach neuen Lieblingsautoren suchst oder die Geschichten hinter den Büchern kennenlernen möchtest - unsere Autorenliste bietet Einblicke in die brillanten Köpfe hinter den Seiten. Verpasse nicht die Gelegenheit, die Faszination der Literatur aus der Perspektive dieser talentierten Autoren zu erleben. Bereite dich darauf vor, von ihrer Leidenschaft für das Geschichtenerzählen mitgerissen zu werden und tauche ein in eine Welt der kreativen Brillanz!';

            return [
                'description' => $description,
                'title' => $title,
                'canonical' => 'https://storysh.de/authors',
                'image' => $image,
                'schema' => self::getListSchemaData($data, $description),
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

        $breadcrumb = 'Startseite > Autoren > ';

        $result['breadcrumb'] = $breadcrumb . $data['completeName'];
        $result['mainEntity'] = [
            '@type' => 'Person',
            'description' => $description,
            'name' => $data['completeName'],
            'givenName' => $data['firstName'],
            'familyName' => $data['lastName'],
            'image' => (DataHandler::isEmpty($data['images'])) ? $data['image'] : $data['images'][0]['media']['src']
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
            'name' => 'Storysh Autoren',
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'Storysh'
            ]
        ];

        $breadcrumb = 'Autoren';

        $result['breadcrumb'] = $breadcrumb;

        return DataHandler::getJsonEncode($result);
    }
}
