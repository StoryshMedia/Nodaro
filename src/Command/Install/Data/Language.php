<?php

namespace Smug\Core\Command\Install\Data;

class Language {
    const LANGUAGES = [
        ['title' => 'Deutsch','locale' => 'de','area' => 'Deutschland','translationAvailable' => true],
        ['title' => 'Englisch','locale' => 'en','area' => 'England','translationAvailable' => true],
        ['title' => 'Spanisch','locale' => 'es','area' => 'Spanien','translationAvailable' => false],
        ['title' => 'Französisch','locale' => 'fr','area' => 'Französisch','translationAvailable' => false]
    ];

    public static function getLanguages(): array
    {
        return self::LANGUAGES;
    }
}