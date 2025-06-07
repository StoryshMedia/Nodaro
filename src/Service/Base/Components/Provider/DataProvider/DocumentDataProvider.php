<?php

namespace Smug\Core\Service\Base\Components\Provider\DataProvider;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Components\Handler\TimeHandler;
use \Exception;
use \DateTime;

/**
 * Class DocumentDataProvider
 * @package Smug\Core\Service\Base\Components\Provider\DataProvider
 */
class DocumentDataProvider
{
    /** Type for offers */
    const TYPE_OFFER = 'Offer';
    /** Type for billings */
    const TYPE_BILLING = 'Billing';
    /** Type for media files */
    const TYPE_MEDIA = 'media';
    /** Type for company files */
    const TYPE_COMPANY = 'company';
    /** Default offer number for previews */
    const DEFAULT_OFFER_NUMBER = '123456';
    /** Default billing number for previews */
    const DEFAULT_BILLING_NUMBER = '123456';
    /** Default billing number for previews */
    const DEFAULT_WARNING_NUMBER = '123456';
    /** Default path for uploads */
    const DEFAULT_UPLOAD_PATH = '/../web/uploads';
    /** Default web path */
    const DEFAULT_WEB_PATH = '/../web';
    /** Default path for attachments */
    const DEFAULT_ATTACHMENT_PATH = '_uploads/attachments';
    /** Default path for attachments */
    const DEFAULT_TEMP_PATH = '_uploads/_temp';
    /** Default path for documents */
    const DEFAULT_DOCUMENT_PATH = '_documents/';
    /** Default path for business letters */
    const DEFAULT_LETTER_PATH = '_documents/letters/';
    /** Default path for business letters */
    const DEFAULT_RESHIPMENT_PATH = '_documents/reshipment/';
    /** Default path for business letters */
    const DEFAULT_SHIPPING_NOTE_PATH = '_documents/shippingNote/';
    /** Default path for billings */
    const DEFAULT_BILLING_PATH = '_documents/billings/';
    /** Default path for requests */
    const DEFAULT_REQUEST_PATH = '_documents/distributorRequests/';
    /** Default path for preview billings */
    const DEFAULT_BILLING_PREVIEW_PATH = '_documents/billings/preview/';
    /** Default path for offers */
    const DEFAULT_OFFER_PATH = '_documents/offers/';
    /** Path for offer sources */
    const OFFER_SOURCE_PATH = '_offer/';
    /** Default path for offer previews */
    const DEFAULT_OFFER_PREVIEW_PATH = '_documents/offers/preview/';
    /** Default path for offers */
    const DEFAULT_REPORT_PATH = '_documents/reports/';

    /**
     * @var array
     */
    const FILE_DIRECTORIES = [
        'customer' => '_uploads/files/customers/_temp',
        'communication' => '_uploads/files/communications/_temp',
        'project' => '_uploads/files/projects/_temp',
        'files' => '_uploads/files/files/_temp',
        'product' => '_uploads/files/product/_temp'
    ];

    /**
     * @var array
     */
    const MEDIA_DIRECTORIES = [
        'tempBanner' => '_uploads/images/_temp/banner',
        'frontModule' => '_uploads/images/media',
        'frontend' => '_uploads/images/media',
        'stories' => '_uploads/images/media/stories',
        'market' => '_uploads/images/media/market',
        'events' => '_uploads/images/media/events',
        'forum' => '_uploads/images/media/forum',
        'contest' => '_uploads/images/media/contests',
        'quote' => '_uploads/images/media/quotes',
        'publications' => '_uploads/images/media/publications',
        'blog' => '_uploads/images/media/blog',
        'blogFiles' => '_uploads/files/media/blog',
        'school' => '_uploads/images/media/schools',
        'contribution' => '_uploads/images/media/contests/contributions',
        'company' => '_uploads/images/company',
        'product' => '_uploads/images/products',
        'users' => '_uploads/images/user',
        'media' => '_uploads/images/media'
    ];

    const MEDIA_ALBUM_MAPPINGS = [
        'frontImage' => 'web',
        'parallax' => 'web',
        'gallery' => 'web',
        'frontSlider' => 'web',
        'iBanner' => 'web',
        'variant' => 'product',
        'product' => 'product',
        'newsletter' => 'newsletter',
        'company' => 'backend',
        'presentations' => 'presentations',
        'logo' => 'backend',
        'user' => 'backend',
        'users' => 'backend',
        'notice' => 'notice',
        'project' => 'project',
        'customer' => 'customer',
        'supplier' => 'supplier',
        'distributor' => 'distributor',
        'communication' => 'communication',
        'campaign' => 'campaign',
    ];

    /**
     * @param array $letter
     * @param $dir
     * @return string
     * @throws Exception
     */
    public static function getLetterPath(array $letter, $dir): string
    {
        $time = TimeHandler::getFormatDate(new DateTime(), TimeProvider::DATE_TIME_FILE_OUTPUT_FORMAT);

        return self::DEFAULT_LETTER_PATH .
            $dir . '/' . $letter['customer']['name'] . '-' . $time . '.pdf';
    }

    /**
     * @param integer|string $number
     * @return string
     */
    public static function getReshipmentPath($number):string
    {
        return self::DEFAULT_RESHIPMENT_PATH . $number . '.pdf';
    }

    /**
     * @param integer|string $number
     * @return string
     */
    public static function getShippingNotePath($number): string
    {
        return self::DEFAULT_SHIPPING_NOTE_PATH . $number . '.pdf';
    }

    /**
     * @param array $request
     * @param string $dir
     * @return string
     */
    public static function getDistributorRequestPath(array $request, string $dir): string
    {
        return self::DEFAULT_REQUEST_PATH .
            $dir . '/' . $request['title'] . '.pdf';
    }

    /**
     * @param array $billing
     * @param string $dir
     * @return string
     */
    public static function getBillingPath(array $billing, string $dir): string
    {
        return self::DEFAULT_BILLING_PATH .
            $dir . '/' . $billing['settings']['prefix']['de'] . $billing['number'] . '.pdf';
    }

    /**
     * @param array $billing
     * @param string $pattern
     * @return string
     */
    public static function getPreviewBillingPath(array $billing, string $pattern): string
    {
        return self::DEFAULT_BILLING_PREVIEW_PATH . $pattern .
            $billing['settings']['settings']['prefix']['de'] . $billing['number'] . '.pdf';
    }

    /**
     * @param string $name
     * @return string
     */
    public static function getBillingDir(string $name): string
    {
        return self::DEFAULT_BILLING_PATH . $name;
    }

    /**
     * @param array $warning
     * @param string $dir
     * @return string
     */
    public static function getWarningPath(array $warning, string $dir): string
    {
        return self::DEFAULT_BILLING_PATH .
            $dir . '/' . $warning['number'] . '.pdf';
    }

    /**
     * @param array $offer
     * @param string $dir
     * @return string
     */
    public static function getOfferPath(array $offer, string $dir): string
    {
        return self::DEFAULT_OFFER_PATH .
            $dir . '/' . $offer['settings']['prefix']['de'] . $offer['offerNumber'] . '.pdf';
    }

    /**
     * @param array $offer
     * @param string $dir
     * @return string
     */
    public static function getOfferConfirmationPath(array $offer, string $dir): string
    {
        return self::DEFAULT_OFFER_PATH .
            $dir . '/' . $offer['offerNumber'] . '.pdf';
    }

    /**
     * @param array $offer
     * @param string $pattern
     * @return string
     */
    public static function getPreviewOfferPath(array $offer, string $pattern): string
    {
        return self::DEFAULT_OFFER_PREVIEW_PATH . $pattern .
            $offer['settings']['settings']['prefix']['de'] . $offer['number'] . '.pdf';
    }

    /**
     * @param array $report
     * @param string $dir
     * @return string
     * @throws Exception
     */
    public static function getReportPath(array $report, string $dir): string
    {
        $report['startDate'] = TimeHandler::getFormatDate(
            new DateTime($report['startDate']),
            TimeProvider::DATE_OUTPUT_FORMAT
        );

        $report['endDate'] = TimeHandler::getFormatDate(
            new DateTime($report['endDate']),
            TimeProvider::DATE_OUTPUT_FORMAT
        );

        $startDate = DataHandler::getReplaceString('.', '_', $report['startDate']);
        $endDate = DataHandler::getReplaceString('.', '_', $report['endDate']);

        return self::DEFAULT_REPORT_PATH . $dir . '/' . $dir . '_' . $startDate . '_' . $endDate . '.pdf';
    }
}
