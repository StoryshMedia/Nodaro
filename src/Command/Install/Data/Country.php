<?php

namespace Smug\Core\Command\Install;

class Country {
    const COUNTRIES = [
        ['title' => 'Deutschland','token' => 'DE','defaultCountry' => '1'],
        ['title' => 'England','token' => 'EN','defaultCountry' => '0'],
        ['title' => 'Frankreich','token' => 'FR','defaultCountry' => '0'],
        ['title' => 'Niederlande','token' => 'NL','defaultCountry' => '0'],
        ['title' => 'Spanien','token' => 'ES','defaultCountry' => '0'],
        ['title' => 'Italien','token' => 'IT','defaultCountry' => '0'],
        ['title' => 'Schweiz','token' => 'CH','defaultCountry' => '0'],
        ['title' => 'Österreich','token' => 'AT','defaultCountry' => '0']
    ];

    public static function getCountries(): array
    {
        return self::COUNTRIES;
    }
}