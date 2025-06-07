<?php

namespace Smug\SearchBundle\Factory;

class SearchFactory
{
    private const EMPTY_RESULT = [
        'label' => 'SEARCH_RESULTS',
        'results' => [],
        'marketing' => []
    ];

    public static function getEmptyResult(): array
    {
        return self::EMPTY_RESULT;
    }
}