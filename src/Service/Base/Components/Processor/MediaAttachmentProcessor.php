<?php

namespace Smug\Core\Service\Base\Components\Processor;

use Smug\Core\DatabaseSetter\Base\BaseSetter;
use Smug\Core\Service\Base\Components\Provider\DataProvider\ExceptionProvider;
use Smug\Core\Service\Base\Interfaces\Provider\ProviderInterface;
use Doctrine\ORM\EntityManagerInterface;
use \Exception;

abstract class MediaAttachmentProcessor implements ProviderInterface
{
    /**
     * @param EntityManagerInterface $em
     * @param BaseSetter $setter
     * @param $attachment
     * @param array $data
     * @param string $class
     * @return array
     */
    public static function process(
        EntityManagerInterface $em,
        BaseSetter $setter,
        $attachment,
        array $data,
        string $class
    ): array {

        $em->beginTransaction();

        try {
            //set the attachment and assign it to the association given in $class
            $newAttachment = $setter->setMediaAsAttachment(
                $attachment,
                $data,
                $class
            );

            $em->commit();

        } catch (Exception $exception) {
            $em->rollback();

            return ExceptionProvider::getException($exception);
        }

        return [
            'success' => true,
            'data' => $newAttachment
        ];
    }
}
