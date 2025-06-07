<?php

namespace Smug\Core\Service\Base\Factory\Backend\Data;

use Doctrine\ORM\EntityManagerInterface;
use Smug\AdministrationBundle\Interface\View\ViewInterface;
use Smug\AdministrationBundle\Service\Components\Factories\ViewBuilder;
use Smug\Core\Context\Context;
use Smug\Core\Service\Base\Interfaces\Backend\BackendDataProviderInterface;

class BackendViewProvider implements BackendDataProviderInterface
{
    /**
     * @inheritdoc
     */
	public static function provideDetail(string $constantsClass, EntityManagerInterface $em, Context $context, array $additional = []): ViewInterface
    {
        /** @var BackendDataConstantsInterface $constants */
        $constants = new $constantsClass();
        $configuration = $constants::getDetailConstants();

        $configuration['config']['type'] = 'detail';

        return self::buildView($configuration, $context);
    }

    /**
     * @inheritdoc
     */
	public static function provideAdd(string $constantsClass, EntityManagerInterface $em, Context $context): ViewInterface
    {
        /** @var BackendDataConstantsInterface $constants */
        $constants = new $constantsClass();
        $configuration = $constants::getAddConstants();

        $configuration['config']['type'] = 'add';

        return self::buildView($configuration, $context);
    }

    /**
     * @inheritdoc
     */
	public static function provideList(string $constantsClass, EntityManagerInterface $em, Context $context, array $additional = []): ViewInterface
    {
        /** @var BackendDataConstantsInterface $constants */
        $constants = new $constantsClass();
        $configuration = $constants::getListConstants();

        $configuration['config']['type'] = 'list';

        return self::buildView($configuration, $context);
    }

    private static function buildView(array $configuration, Context $context): ViewInterface
    {
        return ViewBuilder::build($configuration, $context);
    }
}
