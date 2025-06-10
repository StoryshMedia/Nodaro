<?php

namespace Smug\Core\Command\Install;

class Language {
    const LANGUAGES = [
        ['title' => 'Deutsch','locale' => 'de','area' => 'Deutschland','translationAvailable' => '1'],
        ['title' => 'Englisch','locale' => 'en','area' => 'England','translationAvailable' => '1'],
        ['title' => 'Spanisch','locale' => 'es','area' => 'Spanien','translationAvailable' => '0'],
        ['title' => 'Französisch','locale' => 'fr','area' => 'Französisch','translationAvailable' => '0']
    ];

    public static function getLanguages(): array
    {
        return self::LANGUAGES;
    }
}