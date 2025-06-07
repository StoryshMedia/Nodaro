<?php

namespace Smug\Core\Service\Base\Factory\Seo\Provider;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Interfaces\Seo\SeoDataProviderInterface;

/**
 * Class ForumProvider
 * @package Smug\Core\Service\Base\Factory\Seo\Provider
 */
class ForumProvider implements SeoDataProviderInterface
{
    /**
     * @inheritdoc
     */
    public static function provide(array $data, bool $list = false): array
    {
        $keywords = 'Storysh, Stories, Storish, Rezensionen, Tauschen, Bücher, Autoren, Benutzerbewertungen, Zusammenfassung, Diskussionen, Forum, Thread';

        if ($list === false) {
            $schemaFunction = 'getSchemaData';
            $image = $data['images'][0]['media']['src'];

            foreach ($data['tags'] as $tag) {
                $keywords .= $tag['title'] . ', ';
            }
    
            $description = $data['description'];
            $title = 'Storysh Forum | ' . $data['title'];
    
            $description .= ': Verfasser: ' . $data['user']['name'];
        } else {
            $schemaFunction = 'getListSchemaData';
            $title = 'Storysh | Forum';
            $image = 'https://api.storysh.de/site/img/homeForumHeader.webp';
            $description = 'Storysh | Entdecke die fesselnde Welt der Literatur im Storysh Literaturforum! Tauche ein in leidenschaftliche Diskussionen über zeitlose Klassiker, moderne Bestseller und versteckte Juwelen. Tausche dich mit Gleichgesinnten über Lieblingsautoren, Charaktere und literarische Analyse aus. Trete unserer blühenden Community bei und erweitere deinen literarischen Horizont. Egal, ob du ein passionierter Leser, ein angehender Autor oder ein Literaturbegeisteter bist - hier findest du einen Raum für den lebendigen Austausch über die Welt der Bücher. Willkommen im Storysh Literaturforum, wo Geschichten zum Leben erweckt werden!';
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
        $breadcrumb = 'Community > Forum > ';

        $result = [
            '@context' => 'https://schema.org',
            '@type' => 'DiscussionForumPosting',
            '@id' => 'https://storysh.de/community/forum/' . $data['slug'],
            'breadcrumb' => $breadcrumb . $data['title'],
            'headline' => $data['title'],
            'author' => [
                '@type' => 'Person',
                'user' => $data['user']['name']
            ],
            'interactionStatistic' => [
                '@type' => 'InteractionCounter',
                'interactionType' => 'http://schema.org/CommentAction',
                'userInteractionCount' => DataHandler::getArrayLength($data['messages'])
            ]
        ];

        return DataHandler::getJsonEncode($result);
    }

    /**
     * @inheritdoc
     */
    public static function getListSchemaData(array $data, string $description): string
    {
        $breadcrumb = 'Community > Forum';

        $result = [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            '@id' => 'https://storysh.de/community/forum',
            'breadcrumb' => $breadcrumb,
            'description' => $description,
            'headline' => 'Storysh Forum'
        ];

        return DataHandler::getJsonEncode($result);
    }
}
