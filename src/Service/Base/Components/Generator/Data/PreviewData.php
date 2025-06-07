<?php

namespace Smug\Core\Service\Base\Components\Generator\Data;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Components\Handler\TimeHandler;
use Smug\Core\Service\Base\Components\Provider\DataProvider\TimeProvider;
use \Exception;
use \DateTime;

/**
 * Class PreviewData
 * @package Smug\Core\Base\Service\Components\Generator\Data
 */
class PreviewData
{
    const PREVIEW_CUSTOMER = [
        'name' => 'Preview Kunde',
        'salutation' => 'Herr/Frau',
        'address' => 'Musterstraße 321',
        'zipCode' => '54321',
        'number' => '200001',
        'city' => 'Scheinstadt',
        'mail' => 'info@kundenfirma.de',
        'phone' => '9873216540'
    ];

    const PREVIEW_COMPANY = [
        'name' => 'Musterfirma AG',
        'address' => 'Musterstraße 123',
        'zipCode' => '12345',
        'city' => 'Musterstadt',
        'email' => 'info@musterfirma.de',
        'homepage' => 'www.musterfirma.de',
        'phone' => '0123456789',
        'mobile' => '9876543210',
        'fax' => '9873216540'
    ];

    /**
     * @var string
     */
    const PREVIEW_INTRO = 'Dies ist eine Beispiel Einleitung für Ihr Dokument.';

    /**
     * @var string
     */
    const PREVIEW_ADDITIONAL = 'Dies ist eine Beispiel Schlussformel für Ihr Dokument.';

    /**
     * @var string
     */
    const LOGO_PATH = '/bundles/workPlan/img/company.jpg';

    /**
     * @var string
     */
    const PREVIEW_NUMBER = '12345';

    /**
     * @return array
     * @throws Exception
     */
    public static function getPreviewData(): array
    {
        $count = 0;
        $pages = DataHandler::getArrayChunk(self::getPreviewPositions(), 3, true);

        if (DataHandler::getArrayLength($pages) > 1) {
            foreach ($pages as $key => $page) {
                $pages[$key]['transfer'] = self::getPageTransfer($page);

                if ($count > 0) {
                    $pages[$key]['previewTransfer'] = self::getPageTransfer($pages[$count - 1]);
                } else {
                    $pages[$key]['previewTransfer'] = false;
                }

                $count++;
            }
        }

        $company = self::PREVIEW_COMPANY;

        $company['logo'] = $_SERVER['DOCUMENT_ROOT'] . self::LOGO_PATH;

        return [
            'intro' => self::PREVIEW_INTRO,
            'completeName' => self::PREVIEW_NUMBER,
            'additional' => self::PREVIEW_ADDITIONAL,
            'logoPath' => $_SERVER['DOCUMENT_ROOT'] . self::LOGO_PATH,
            'company' => $company,
            'date' => TimeHandler::getFormatDate(new DateTime(), TimeProvider::DATE_OUTPUT_FORMAT),
            'customer' => self::PREVIEW_CUSTOMER,
            'tax' => [
                'value' => 19
            ],
            'totalValueNet' => 500.00,
            'valueNet' => 500.00,
            'taxValue' => 95.00,
            'valueGross' => 595.00,
            'totalValueGross' => 595.00,
            'pages' => $pages
        ];
    }

    /**
     * @param array $page
     * @return float
     */
    private static function getPageTransfer(array $page): float
    {
        $transfer = 0.00;

        foreach ($page as $position) {
            if (!DataHandler::isArray($position)) {
                continue;
            }

            $transfer += $position['oncePrice'];
        }

        return $transfer;
    }

    /**
     * @return array
     */
    private static function getPreviewPositions(): array
    {
        $positions = [];

        for ($i = 1; $i <= 5; $i++) {
            $description = '';

            if ($i % 2 === 0) {
                $description = 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor
                 invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua';
            }

            $positions[] = [
                'number' => $i,
                'orderNumber' => '12345',
                'description' => $description,
                'name' => 'Preview Position' . $i,
                'quantity' => 1,
                'taxValue' => 19.00,
                'oncePrice' => 100.00,
                'wholePrice' => 119.00
            ];
        }

        return $positions;
    }
}
