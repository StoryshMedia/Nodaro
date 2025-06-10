<?php

namespace Smug\Core\Command\Install\Data;

class Country {
    const COUNTRIES = [
        ['title' => 'Deutschland','token' => 'DE','defaultCountry' => true],
        ['title' => 'England','token' => 'EN','defaultCountry' => false],
        ['title' => 'Frankreich','token' => 'FR','defaultCountry' => false],
        ['title' => 'Niederlande','token' => 'NL','defaultCountry' => false],
        ['title' => 'Spanien','token' => 'ES','defaultCountry' => false],
        ['title' => 'Italien','token' => 'IT','defaultCountry' => false],
        ['title' => 'Schweiz','token' => 'CH','defaultCountry' => false],
        ['title' => 'Österreich','token' => 'AT','defaultCountry' => false]
    ];

    public static function getCountries(): array
    {
        return self::COUNTRIES;
    }
}