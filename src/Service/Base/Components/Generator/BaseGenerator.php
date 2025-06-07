<?php

namespace Smug\Core\Service\Base\Components\Generator;

use Smug\Core\Entity\Base\BaseModel;
use Smug\Core\Entity\File\Document\Offer;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Components\Handler\TimeHandler;
use Smug\Core\Service\Base\Components\Preparer\DocumentNumberPreparer;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;

/**
 * Class DocumentGenerator
 * @package Smug\Core\Service\Base\Components\Generator
 */
class BaseGenerator
{
    /** @var EntityManagerInterface $em */
    public EntityManagerInterface $em;

    /**
     * DocumentGenerator constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param array $data
     * @param string $class
     * @param string $propertyName
     * @param bool $update
     * @return string
     * @throws NonUniqueResultException
     */
    public function getDocumentNumber(array $data, string $class, string $propertyName, bool $update): string
    {
        if (!DataHandler::doesKeyExists($propertyName, $data)) {
            /** @var BaseModel $last_entity */
            $last_entity = $this->em->createQueryBuilder()
                ->select('c')
                ->from($class, 'c')
                ->orderBy('c.id', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

            if ($last_entity !== null) {
                if ($class === Offer::class) {
                    $last_entity = $last_entity->getOfferNumber();
                } else {
                    $last_entity = $last_entity->getBillingNumber();
                }
            } else {
                $settings = $data['settings'];

                $last_entity = (DataHandler::doesKeyExists('billingNumberStart', $settings)) ? $settings['billingNumberStart'] : $settings['offerNumberStart'];
            }

            $number = DocumentNumberPreparer::generateDocumentNumber(
                $data['settings']['numberFormat'],
                $last_entity,
                $data['settings']['prefix']['de']
            );
        } else {
            // remove prefix from billing number to avoid something like "R-R-12345"
            $number = ($update === false) ? $data[$propertyName] : DataHandler::getReplaceString(
                $data['settings']['prefix']['de'],
                '',
                $data[$propertyName]
            );
        }

        return $number;
    }

    /**
     * @param array $data
     * @param string $number
     * @param bool $billing
     * @param bool $cancel
     * @return array
     */
    public function preparePositions(array $data, string $number, bool $billing = false, bool $cancel = false): array
    {
        $i = 1;
        $refactoredPositions = [];

        if (DataHandler::doesKeyExists('tax', $data)) {
            $taxArray = (DataHandler::isArray($data['tax'])) ? $data['tax'] : $data['tax']->toArray();

            $taxValue = 0.00;
            if (DataHandler::doesKeyExists('id', $taxArray)) {
                $taxValue = DataHandler::getFormattedNumber($taxArray['value'], 2);
            }
        }

        foreach ($data['positions'] as $position) {
            if (!DataHandler::doesKeyExists('quantity', $position)) {
                $position['quantity'] = $position['hours'];
            }

        	if ($position['quantity'] === 0) {
        		continue;
	        }
	
	        $refactoredPosition = [
		        'number' => $i,
		        'orderNumber' => (DataHandler::doesKeyExists('orderNumber', $position)) ? $position['orderNumber'] : $number . '-' . $i,
		        'description' => $position['description'],
		        'name' => $position['name'],
		        'quantity' => $position['hours'],
		        'hours' => $position['hours'],
		        'service' => (DataHandler::doesKeyExists('service', $position)) ? $position['service'] : []
	        ];
	
        	if (DataHandler::doesKeyExists('bookings', $position)) {
		        foreach ($position['bookings'] as $bookingKey => $booking) {
		        	if (DataHandler::doesKeyExists('ignoreOnBilling', $booking) && $booking['ignoreOnBilling'] === true) {
				        $position['bookings'] = DataHandler::unsetArrayElement($position['bookings'], $bookingKey);
			        }
		        }
		
		        $refactoredPosition['bookings'] = $position['bookings'];
	        }

            if (DataHandler::doesKeyExists('id', $position)) {
                $refactoredPosition['id'] = $position['id'];
            }

            if ($billing === true) {
                $refactoredPosition['oncePrice'] = ($cancel === false) ? $position['oncePrice'] : $position['oncePrice'] * -1;
                $refactoredPosition['wholePrice'] = ($cancel === false) ? $position['wholePrice'] : $position['wholePrice'] * -1;
                $refactoredPosition['quantity'] = TimeHandler::getQuantityFromHours($position['hours']);
            } else {
                $refactoredPosition['oncePrice'] = $position['oncePrice'];
                $refactoredPosition['wholePrice'] = $position['wholePrice'];
            }

            if (DataHandler::doesKeyExists('tax', $data)) {
                $refactoredPosition['taxValue'] = $taxValue;
            }

            $refactoredPositions[] = $refactoredPosition;

            $i++;
        }

        return $refactoredPositions;
    }

    /**
     * @param array $data
     * @return array
     */
    public function checkTexts(array $data): array
    {
        if (!DataHandler::doesKeyExists('introduction', $data)) {
            $data['introduction'] = '';
        }

        if (!DataHandler::doesKeyExists('closure', $data)) {
            $data['closure'] = '';
        }

        return $data;
    }
}
