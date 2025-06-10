<?php

namespace Smug\SearchBundle\Factory;

use Smug\Core\Service\Base\Components\Handler\DataHandler;

class PaginationPageFactory
{
    private const EMPTY_PAGES = [
        'start' => 1,
        'end' => 1,
        'preSteps' => [],
        'mainSteps' => [],
        'postSteps' => []
    ];

    public static function getPaginationPages(int $pages, int $page) : array
    {
        $arPages = self::EMPTY_PAGES;
        $arPages['end'] = $pages;
        $arPages = self::buildMainSteps($arPages, $pages, $page);

        if ($arPages['end'] === 1) {
            $arPages['end'] = '';
        }

        if (DataHandler::isEmpty($arPages['mainSteps'])) {
            return $arPages;
        }

        if ($arPages['mainSteps'][0] > 4) {
            $gap = $arPages['mainSteps'][0] - 1;
            $steps = self::getPaginationDivider($gap);

            $count = $steps;
            for ($count; $count >= 1; $count--) {
                $arPages['preSteps'][] = intdiv($gap, $count);
            }
        }

        if (($pages - DataHandler::getLastArrayElement($arPages['mainSteps'])) > 4) {
            $gap = $pages - DataHandler::getLastArrayElement($arPages['mainSteps']);
            $steps = self::getPaginationDivider($gap);

            $count = $steps;
            for ($count; $count >= 1; $count--) {
                $arPages['postSteps'][] = $page + intdiv($gap, $count);
            }
        }

        if ($arPages['end'] === 1) {
            $arPages['end'] = '';
        }

        return $arPages;
    }

    protected static function buildMainSteps(array $arPages, int $pages, int $page): array
    {
        if ($page > 1) {
            if ($page > 4) {
                $arPages['mainSteps'][] = $page - 3;
                $arPages['mainSteps'][] = $page - 2;
                $arPages['mainSteps'][] = $page - 1;
                $arPages['mainSteps'][] = $page;
            } else {
                if ($page === 4) {
                    $arPages['mainSteps'][] = 2;
                    $arPages['mainSteps'][] = 3;
                    $arPages['mainSteps'][] = $page;
                }
                if ($page === 3) {
                    $arPages['mainSteps'][] = 2;
                    $arPages['mainSteps'][] = $page;
                }
                if ($page === 2) {
                    $arPages['mainSteps'][] = $page;
                }
            }
        }

        if (($pages - $page) > 3) {
            $arPages['mainSteps'][] = $page + 1;
            $arPages['mainSteps'][] = $page + 2;
            $arPages['mainSteps'][] = $page + 3;
        } else {
            if (($pages - $page) === 3) {
                $arPages['mainSteps'][] = $page + 1;
                $arPages['mainSteps'][] = $page + 2;
            }
            if (($pages - $page) === 2) {
                $arPages['mainSteps'][] = $page + 1;
            }
        }

        if (($arPages['start'] < $arPages['end']) && DataHandler::isEmpty($arPages['mainSteps'])) {
            $arPages['mainSteps'][] = $arPages['end'];
        }

        return $arPages;
    }

    protected static function getPaginationDivider(int $size): int
    {
        if ($size > 1000) {
            return 5;
        }

        if ($size > 100) {
            return 3;
        }

        if ($size > 10) {
            return 2;
        }

        return 2;
    }
}