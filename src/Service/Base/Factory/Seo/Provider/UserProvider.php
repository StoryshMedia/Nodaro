<?php

namespace Smug\Core\Service\Base\Factory\Seo\Provider;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Interfaces\Seo\SeoDataProviderInterface;

/**
 * Class UserProvider
 * @package Smug\Core\Service\Base\Factory\Seo\Provider
 */
class UserProvider implements SeoDataProviderInterface
{
    /**
     * @inheritdoc
     */
    public static function provide(array $data, bool $list = false): array
    {
        $image = '';
        $keywords = 'Storysh, Stories, Storish, Rezensionen, Tauschen, Bücher, Autoren, Benutzerbewertungen, Zusammenfassung, Diskussionen, User, Community';
        
        if ($list === false) {
            $description = $data['name'];
            $keywords .= ', ' . $data['name'];
            $title = 'Storysh Community | ' . $data['name'];
    
            $description .= ' ' . $data['additionalInformation'];
    
            if (DataHandler::doesKeyExists('images', $data) && DataHandler::isArray($data['images'])) {
                if (!DataHandler::isEmpty($data['images'])) {
                    $image = $data['images'][0]['media']['src'];
                }
            }
        } else {
            $description = 'Willkommen in unserer bezaubernden Literaturcommunity - einem Ort, an dem Leser und Autoren ihre Leidenschaft für Geschichten teilen. Tauche ein in die unendlichen Weiten der Literatur, entdecke neue Welten, lerne fesselnde Charaktere kennen und erlebe den Zauber von Worten. Unsere Community bietet Raum für anregende Diskussionen über Bücher aller Genres, Schreibwerkstätten, um deine kreativen Ideen zu entfalten, und die Möglichkeit, inspirierende Autoren kennenzulernen. Egal, ob du ein Bücherwurm, ein Schreibenthusiast oder einfach nur neugierig bist - bei uns findest du Gleichgesinnte, die die Magie der Literatur lieben. Tritt ein in unsere Literaturcommunity und lass dich von der Kraft der Worte verzaubern!';
            $title = 'Storysh | Community';
            $image = 'https://api.storysh.de/site/img/PatreonBanner.webp';
        }

        return [
            'description' => $description,
            'title' => $title,
            'image' => $image,
            'keywords' => trim(preg_replace('/\s+/', ' ', $keywords))
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getSchemaData(array $data, string $description): string
    {
        return '';
    }
}
