<?php

namespace Smug\Core\Service\Base\Components\Generator;

use Smug\Core\Service\Base\Components\Generator\Data\PreviewData;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use \Exception;

/**
 * Class PreviewGenerator
 * @package Smug\Core\Service\Base\Components\Generator
 */
class PreviewGenerator
{
    /**
     * @param array $settings
     * @param $number
     * @param $documentType
     * @return array
     * @throws Exception
     */
    public function generatePreviewDocument(array $settings, $number, $documentType): array
    {
        $previewData = PreviewData::getPreviewData();

        return [
            'dataHeader' => [$settings['css'], $documentType],
            'htmlHeader' => [
                $previewData['company'],
                $previewData['customer'],
                $settings['header'],
                $previewData['logoPath'],
                $settings['numberSuffix'] . $number
            ],
            'htmlBody' => [
                'subjects' => $previewData['subjects'],
                'intro' => DataHandler::getTextWithLineBreaks($previewData['intro']),
                'additional' => DataHandler::getTextWithLineBreaks($previewData['additional']),
                'totalValue' => $previewData['totalValue'],
                'settings' => $settings
            ],
            'customer' => $previewData['customer'],
            'settings' => $settings,
            'number' => $number
        ];
    }
}
