<?php

namespace Smug\Core\Service\Base\Components\Provider\DataProvider;

class TimeProvider
{
    const MONTHS = [
        1 => 'Januar',
        2 => 'Februar',
        3 => 'März',
        4 => 'April',
        5 => 'Mai',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'August',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Dezember'
    ];

    const DATE_TIME_MONTHS_MAPPING = [
        'January' => 'Januar',
        'February' => 'Februar',
        'March' => 'März',
        'April' => 'April',
        'May' => 'Mai',
        'June' => 'Juni',
        'July' => 'Juli',
        'August' => 'August',
        'September' => 'September',
        'October' => 'Oktober',
        'November' => 'November',
        'December' => 'Dezember'
    ];

    const SNIPPET_MONTHS = [
        1 => 'JANUARY',
        2 => 'FEBRUARY',
        3 => 'MARCH',
        4 => 'APRIL',
        5 => 'MAY',
        6 => 'JUNE',
        7 => 'JULY',
        8 => 'AUGUST',
        9 => 'SEPTEMBER',
        10 => 'OCTOBER',
        11 => 'NOVEMBER',
        12 => 'DECEMBER'
    ];

    const DATE_TIME_MONTHS = [
        1 => 'January',
        2 => 'February',
        3 => 'March',
        4 => 'April',
        5 => 'May',
        6 => 'June',
        7 => 'July',
        8 => 'August',
        9 => 'September',
        10 => 'October',
        11 => 'November',
        12 => 'December'
    ];

    const MONTH_DATA = [
        1 => [
            'start' => 1,
            'end' => 31
        ],
        2 => [
            'start' => 1,
            'end' => 28
        ],
        3 => [
            'start' => 1,
            'end' => 31
        ],
        4 => [
            'start' => 1,
            'end' => 30
        ],
        5 => [
            'start' => 1,
            'end' => 31
        ],
        6 => [
            'start' => 1,
            'end' => 30
        ],
        7 => [
            'start' => 1,
            'end' => 31
        ],
        8 => [
            'start' => 1,
            'end' => 31
        ],
        9 => [
            'start' => 1,
            'end' => 30
        ],
        10 => [
            'start' => 1,
            'end' => 31
        ],
        11 => [
            'start' => 1,
            'end' => 30
        ],
        12 => [
            'start' => 1,
            'end' => 31
        ],
    ];

    const DATE_OUTPUT_FORMAT = 'd.m.Y';

    const DATE_OUTPUT_FORMAT_SITEMAP = 'Y-M-D';

    const DATE_OUTPUT_FORMAT_INT = 'Y-m-d';

    const DATE_OUTPUT_FORMAT_SOLR = 'Y-m-dTH:iZ';

    const DATE_TIME_OUTPUT_FORMAT = 'd.m.Y G:i';

    const DATE_TIME_FILE_OUTPUT_FORMAT = 'Ymd';

    const TIME_OUTPUT_FORMAT = 'G:i';

    const TIME_EVENT_OUTPUT_FORMAT = 'H:i';

    const TIME_OUTPUT_HOUR_FORMAT = 'G';

    const ISO_8601 = 'c';

    const TIME_OUTPUT_MINUTE_FORMAT = 'i';

    const TIME_OUTPUT_SECOND_FORMAT = 's';

    const DOCUMENT_NUMBER_TIME_FORMAT = 'Ymd';

    const TIME_STAMP_FORMAT = 'U';
}
