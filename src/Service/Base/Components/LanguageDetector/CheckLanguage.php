<?php

namespace Smug\Core\Service\Base\Components\LanguageDetector;

use Smug\Core\Service\Base\Components\Handler\DataHandler;

class CheckLanguage
{
    const detectLanguages = [];

    const allowedLanguages = ['en', 'de', 'fr', 'es', 'it'];

    const languageStructuredDataLabels = [
        'en' => 'English',
        'de' => 'German',
        'fr' => 'French',
        'es' => 'Espaniol',
        'it' => 'Italian'
    ];

    /**
     * @param string $text
     * @return bool
     */
    public static function checkForLanguage(string $text): bool
    {
        $scores = LanguageDetector::detect(
            $text,
            self::detectLanguages
        )->getScores();


        return self::isLanguageAllowed($scores);
    }
    
    /**
     * @param array $scores
     * @return bool
     */
    private static function isLanguageAllowed(array $scores): bool
    {
        $isAllowed = false;

        foreach (self::allowedLanguages as $allowedLanguage) {
            if (!DataHandler::isElementInArray($allowedLanguage, $scores)) {
                continue;
            }

            $position = DataHandler::getPositionInArray($allowedLanguage, $scores);

            if ($position === false) {
                continue;
            }

            if ($position < 5) {
                $isAllowed = true;
            }
        }

        return $isAllowed;
    }
}
