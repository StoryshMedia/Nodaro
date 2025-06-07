<?php

namespace Smug\Core\Service\Base\Components\Provider\DataProvider;

class MentionProvider
{
    public static function getMentions(string $text):array
    {
        $result = [];
        preg_match_all('/(data-mention="(.*?)")/', $text, $subResult);

        foreach ($subResult as $subItem) {
            foreach ($subItem as $item) {
                if (substr($item, 0, 1) === '@') {
                    $result[] = $item;
                }
            }
        }

        return $result;
    }
}
