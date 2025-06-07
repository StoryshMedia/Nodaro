<?php

namespace Smug\Core\Service\Base\Components\Generator;

use Smug\Core\Entity\File\Document\Billing;
use Smug\Core\Entity\File\Document\Offer;
use Smug\Core\Entity\File\Document\WarningSettings;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Components\Handler\TimeHandler;
use Smug\Core\Service\Base\Components\Provider\DataProvider\BarcodeProvider;
use Smug\Core\Service\Base\Components\Provider\DataProvider\TimeProvider;
use Container4KxaND0\getApiPlatform_Listener_View_RespondService;
use Doctrine\ORM\NonUniqueResultException;
use \Exception;
use \DateTime;

/**
 * Class DocumentGenerator
 * @package Smug\Core\Service\Base\Components\Generator
 */
class DocumentGenerator extends BaseGenerator
{
    /**
     * @param array $letter
     * @return array
     * @throws Exception
     */
    public function generateLetter(array $letter): array
    {
        $return = [
            'logoPath' => $letter['company']['logo'],
            'company' => $letter['company'],
            'customer' => $letter['customer'],
            'date' => TimeHandler::getFormatDate(new DateTime(), TimeProvider::DATE_OUTPUT_FORMAT),
            'headline' => $letter['title'],
            'text' => $letter['text']
        ];

        if ($letter['contact'] !== null) {
            $return['contact'] = $letter['contact'];
        }

        return $return;
    }

    /**
     * @param array $request
     * @return array
     * @throws Exception
     */
    public function generateDistributorRequest(array $request): array
    {
        $pages = DataHandler::getArrayChunk(
            $request['positions'],
            3,
            true
        );

        $date = new DateTime();
        $barcodeProvider = new BarcodeProvider();
        $barcode = $barcodeProvider->generateCode11Barcode("00000" . $date->getTimestamp());

        return [
            'id' => (DataHandler::doesKeyExists('id', $request) ? $request['id'] : ''),
            'description' => $request['description'],
            'company' => $request['company'],
            'date' => TimeHandler::getFormatDate($date, TimeProvider::DATE_OUTPUT_FORMAT),
            'distributor' => $request['distributor'],
            'pages' => $pages,
            'barcode' => [
                'code' => $barcode->generate(),
            ],
            'positions' => $request['positions'],
            'completeName' => $request['title'],
            'headline' => $request['title'],
            'title' => $request['title']
        ];
    }

    /**
     * @param array $billing
     * @param bool $update
     * @param bool $cancel
     * @return array
     * @throws NonUniqueResultException
     */
    public function generateBilling(array $billing, $update = false, $cancel = false): array
    {
    	$pattern = $billing['settings'];
        $billing['settings'] = DataHandler::getJsonDecode($billing['settings']->getSettings(), true);

        $billing = $this->checkTexts($billing);

        $billingNumber = $this->getDocumentNumber(
            $billing,
            Billing::class,
            'billingNumber',
            $update
        );

        $billedCards = $this->preparePositions($billing, $billingNumber, true, $cancel);
        $count = 0;

        $pages = DataHandler::getArrayChunk($billedCards, 3, true);

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

        $billing['settings']['numberFormat'] = $billingNumber;
        
	    $billing['totalValueNet'] = DataHandler::getFloatFromString($billing['totalValueNet']);
	    $billing['totalValueGross'] = DataHandler::getFloatFromString($billing['totalValueGross']);
	    $billing['taxValue'] = DataHandler::getFloatFromString($billing['taxValue']);

        if ($cancel === true) {
	        $billing['totalValueNet'] = $billing['totalValueNet'] * - 1;
	        $billing['totalValueGross'] = $billing['totalValueGross'] * - 1;
	        $billing['taxValue'] = $billing['taxValue'] * - 1;
        }
	
	    $barcodeProvider = new BarcodeProvider();
	    $barcode = $barcodeProvider->generateCode11Barcode("00001" . $billingNumber);
	    
        // TODO Prefix for Cancel Billing Number in pattern
        $return = [
            'logoPath' => $_SERVER['DOCUMENT_ROOT'] . $billing['company']->getLogo(),
            'company' => $billing['company'],
	        'barcode' => [
		        'code' => $barcode->generate(),
	        ],
            'originalBillingNumber' => ($cancel === false) ? null : $billing['billingNumber'],
            'billingNumber' => ($cancel === false) ? $billingNumber : 'S-' . $billingNumber,
            'pattern' => $pattern,
            'date' => TimeHandler::getFormatDate(new DateTime(), TimeProvider::DATE_OUTPUT_FORMAT),
            'totalValueNet' => $billing['totalValueNet'],
            'taxValue' => $billing['taxValue'],
            'totalValueGross' => $billing['totalValueGross'],
            'pages' => $pages,
            'positions' => DataHandler::getJsonEncode($billedCards),
            'billedCards' => $billedCards,
            'completeName' => ($cancel === false) ? $billingNumber : 'S-' . $billingNumber,
            'isNet' => (DataHandler::getIntFromString($billing['tax']->getValue()) > 0) ? false : true,
            'source' => 'live',
            'number' => ($cancel === false) ? $billingNumber : 'S-' . $billingNumber,
            'headline' => $billing['settings']['prefix']['de'] . $billingNumber
        ];

        if ($cancel === true) {
            $return = DataHandler::mergeArray($return, [
                'cancel' => [
                    'billingNumber' => $billing['billingNumber'],
                    'totalValueGross' => $billing['totalValueGross'],
                    'date' => $billing['date']
                ]
            ]);
        }

        if ($cancel === true || $update === true) {
            $return['id'] = $billing['id'];
            $return['delivered'] = $billing['delivered'];
            $return['payed'] = $billing['payed'];
            $return['warned'] = $billing['warned'];
            $return['clearedDate'] = 'NOT_CLEARED';
        }

        return DataHandler::mergeArray($billing, $return);
    }

    /**
     * @param array $offer
     * @param bool $confirmation
     * @return array
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function generateOffer(array $offer, $confirmation = false): array
    {
        if ($confirmation === false) {
            $offerNumber = $this->getDocumentNumber(
                $offer,
                Offer::class,
                'offerNumber',
                false
            );

            $offer = $this->checkTexts($offer);

            $offerPositions = $this->preparePositions($offer, $offerNumber);
            $headline = $offer['settings']['prefix']['de'] . $offerNumber;

            $count = 0;

            $pages = DataHandler::getArrayChunk($offerPositions, 3, true);

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
        } else {
            if (!DataHandler::doesKeyExists('confirmationPrefix', $offer['settings'])) {
                $offer['settings']['confirmationPrefix']['de'] = 'AB-';
            }

            $pages = $offer['positions'];
            $offerNumber = $offer['settings']['confirmationPrefix']['de'] . $offer['offerNumber'];
            $headline = $offerNumber;
        }

        $barcodeProvider = new BarcodeProvider();
        $barcode = $barcodeProvider->generateCode11Barcode("00001" . $offerNumber);

        return DataHandler::mergeArray(
            $offer,
            [
                'logoPath' => $_SERVER['DOCUMENT_ROOT'] . $offer['company']->getLogo(),
                'company' => $offer['company'],
                'barcode' => [
                    'code' => $barcode->generate(),
                ],
                'date' => TimeHandler::getNewDateObject(),
                'totalValueNet' => $offer['totalValueNet'],
                'taxValue' => $offer['taxValue'],
                'totalValue' => $offer['totalValue'],
                'pages' => $pages,
                'completeName' => $offerNumber,
                'isNet' => (DataHandler::getIntFromString($offer['taxObj']->getValue()) > 0) ? false : true,
                'introduction' => $offer['introduction'],
                'closure' => $offer['closure'],
                'source' => 'live',
                'offerNumber' => $offerNumber,
                'headline' => $headline
            ]
        );
    }

    /**
     * @param array $billing
     * @param array $existingWarnings
     * @return array
     * @throws Exception
     */
    public function generateWarning(array $billing, array $existingWarnings): array
    {
        $introduction = '';
        $closure = '';
        $firstWarningDate = null;
        $secondWarningDate = null;
        $warningPayGoalDate = TimeHandler::getNewDateObject()->modify('+14 days');

        $daysSinceBilling = TimeHandler::getDaysBetweenDates(TimeHandler::getNewDateObject(), $billing['deliveryDate']);

        $warnCount = $billing['warned'] + 1;

        $company = $billing['company'];
        /** @var WarningSettings $pattern */
        $pattern = $company->getWarningPattern();
	    $billing['totalValueGross'] = DataHandler::getFloatFromString($billing['totalValueGross']);

	    $settings = DataHandler::getJsonDecode($pattern->getSettings(), true);
	    $isNet = (DataHandler::getIntFromString($billing['tax']['value']) > 0) ? false : true;
        
        $reminderFee = DataHandler::getFloatFromString(
        	($isNet === true) ? $settings['businessLatePaymentInterests'] : $settings['privateLatePaymentInterests']
        );

        if ($settings['calculateFeeForFirstWarning'] === true && $warnCount === 1) {
            $reminderFee = 0;
        }

	    $reminderValue = DataHandler::roundNumber(($billing['totalValueGross'] / 100) * $reminderFee);

        $reminderValue = DataHandler::roundNumber(
            ($reminderValue / 365) * $daysSinceBilling
        );

	    if (DataHandler::getArrayLength($existingWarnings) > 0) {
	        $firstWarningDate = $existingWarnings[0]->getDeliveryDate();

	        if (DataHandler::getArrayLength($existingWarnings) > 1) {
                $secondWarningDate = $existingWarnings[1]->getDeliveryDate();

                // if it is the last warning reduce the warningPayGoalDate from 14 to 7 days in the future
                if (DataHandler::getArrayLength($existingWarnings) === 2) {
                    $warningPayGoalDate = TimeHandler::getNewDateObject()->modify('+7 days');
                }
            }
        }
	
        switch ($warnCount) {
	        case 1:
		        $warnValue = $settings['reminderFeeFirstWarning'];
		        $closure = $settings['additionalTextFirstWarning-de'];
		        $introduction = $settings['introductionTextFirstWarning-de'];
		        break;
	        case 2:
		        $warnValue = $settings['reminderFeeSecondWarning'];
                $closure = $settings['additionalTextSecondWarning-de'];
                $introduction = $settings['introductionTextSecondWarning-de'];
		        break;
	        case 3:
		        $warnValue = $settings['reminderFeeLastWarning'];
                $closure = $settings['additionalTextLastWarning-de'];
                $introduction = $settings['introductionTextLastWarning-de'];
		        break;
	        default:
		        $warnValue = 0;
        }
        
        $billSetting['headline'] = $warnCount . '. Mahnung';
	
	    $barcodeProvider = new BarcodeProvider();
	    $barcode = $barcodeProvider->generateCode11Barcode("00001" . $billing['billingNumber'] . $warnCount);

        $warning = [
            'logoPath' => $_SERVER['DOCUMENT_ROOT'] . $billing['company']->getLogo(),
            'company' => $billing['company'],
            'warnCount' => $warnCount,
            'warnValue' => $warnValue,
            'totalWarningValue' => DataHandler::getNumberWithDecimals(
                $billing['totalValueGross'] + $warnValue + $reminderValue,
                2
            ),
            'originalBillingNumber' => $billing['billingNumber'],
            'pattern' => $pattern,
            'originalBillingDate' => $billing['date'],
            'firstWarningDate' => TimeHandler::getFormatDate($firstWarningDate, TimeProvider::DATE_OUTPUT_FORMAT),
            'secondWarningDate' => TimeHandler::getFormatDate($secondWarningDate, TimeProvider::DATE_OUTPUT_FORMAT),
            'warningPayGoalDate' => TimeHandler::getFormatDate($warningPayGoalDate, TimeProvider::DATE_OUTPUT_FORMAT),
            'reminderFee' => $reminderFee,
            'reminderValue' => $reminderValue,
            'barcode' => [
                'code' => $barcode->generate(),
            ],
            'date' => TimeHandler::getFormatDate(new DateTime(), TimeProvider::DATE_OUTPUT_FORMAT),
            'customer' => $billing['customer'],
            'originalBillingValue' => $billing['totalValueGross'],
            'completeName' => 'M-' . $billing['billingNumber'] . '-' . $warnCount,
            'source' => 'live',
            'number' => 'M-' . $billing['billingNumber'] . '-' . $warnCount,
            'headline' => 'M-' . $billing['billingNumber'] . '-' . $warnCount
        ];

        $warning['closure'] = $this->replaceWarningTextWithData($closure, $warning);
        $warning['introduction'] = $this->replaceWarningTextWithData($introduction, $warning);

        return $warning;
    }

    /**
     * @param array $shippingNote
     * @return array
     * @throws Exception
     */
    public function generateShippingNote(array $shippingNote): array
    {
        $offerPositions = $this->prepareShippingNotePositions($shippingNote['positions']);

        return [
            'logoPath' => $_SERVER['DOCUMENT_ROOT'] . $shippingNote['company']['logo'],
            'date' => TimeHandler::getFormatDate(new DateTime(), TimeProvider::DATE_OUTPUT_FORMAT),
            'positions' => $offerPositions,
            'source' => 'live',
        ];
    }

    /**
     * @param array $report
     * @return array
     */
    public function generateReport(array $report): array
    {
        $accountDetails = [];
        $accounts = $this->prepareReportAccounts($report['selectedReportAccounts']);

        $overviewPages = DataHandler::getArrayChunk($accounts, 8, true);

        foreach ($accounts as $account) {
            $accountDetails[] = $this->getDetailReportPages($account);
        }

        return [
            'overview' => $overviewPages,
            'details' => $accountDetails
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

            $transfer += DataHandler::getFloatFromString($position['wholePrice']);
        }

        return $transfer;
    }

    /**
     * @param array $positions
     * @return array
     */
    private function prepareShippingNotePositions(array $positions): array
    {
        $i = 1;
        $refactoredPositions = [];

        foreach ($positions as $position) {
            if (DataHandler::doesKeyExists('product', $position)) {
                $description = $position['product']['shortDescription'];
            } else {
                // TODO add service description PAYMENT OR DELIVERY
                $description = '';
            }

            $refactoredPositions[] = [
                'number' => $i,
                'orderNumber' => $position['orderNumber'],
                'description' => $description,
                'name' => $position['name'],
                'quantity' => $position['quantity'],
            ];

            $i++;
        }

        return $refactoredPositions;
    }

    /**
     * @param array $account
     * @return array
     */
    private function getDetailReportPages(array $account): array
    {
        $pages = [];

        $detailPages = DataHandler::getArrayChunk($account['bookings'], 20, true);

        foreach ($detailPages as $detailPage) {
            $pages[] = [
                'bookings' => $detailPage,
                'name' => $account['name']
            ];
        }

        return $pages;
    }

    /**
     * @param array $accounts
     * @return array
     */
    private function prepareReportAccounts(array $accounts): array
    {
        $return = [];

        foreach ($accounts as $account) {
            if (!DataHandler::doesKeyExists('bookings', $account)) {
                $account['bookings'] = [];
            }

            $subResult = [
                'openingBalance' => $account['openingBalance'],
                'name' => $account['name'],
                'bookings' => []
            ];

            if (DataHandler::doesKeyExists('assets', $account['bookings'])) {
                foreach ($account['bookings']['assets'] as $asset) {
                    $subResult['bookings'][] = $this->getBooking($asset);
                }
            }

            if (DataHandler::doesKeyExists('dues', $account['bookings'])) {
                foreach ($account['bookings']['dues'] as $due) {
                    $subResult['bookings'][] = $this->getBooking($due);
                }
            }

            $return[] = $subResult;
        }

        return $return;
    }

    /**
     * @param array $booking
     * @return array
     */
    private function getBooking(array $booking): array
    {
        return [
            'name' => $booking['name'],
            'documentNumber' => $booking['documentNumber'],
            'gain' => $booking['gain'],
            'cost' => $booking['cost'],
            'date' => $booking['date'],
            'form' => $booking['form']['name'],
        ];
    }

    /**
     * @param string $text
     * @param array $data
     * @return string
     */
    private function replaceWarningTextWithData(string $text, array $data): string
    {
        $text = DataHandler::getReplaceString('{originalBillingNumber}', $data['originalBillingNumber'], $text);
        $text = DataHandler::getReplaceString('{originalBillingDate}', $data['originalBillingDate'], $text);
        $text = DataHandler::getReplaceString('{originalBillingValue}', $data['originalBillingValue'], $text);
        $text = DataHandler::getReplaceString('{firstWarningDate}', $data['firstWarningDate'], $text);
        $text = DataHandler::getReplaceString('{secondWarningDate}', $data['secondWarningDate'], $text);
        $text = DataHandler::getReplaceString('{warningPayGoalDate}', $data['warningPayGoalDate'], $text);
        $text = DataHandler::getReplaceString('{totalWarningValue}', $data['totalWarningValue'], $text);

        return $text;
    }
}
