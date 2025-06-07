<?php

namespace Smug\Core\Service\Base\Interfaces\Backend;

use Doctrine\ORM\EntityManagerInterface;
use Smug\AdministrationBundle\Interface\View\ViewInterface;
use Smug\Core\Context\Context;

interface BackendDataProviderInterface
{
    /**
     * @param string $slug
     * @param EntityManagerInterface $em
     * @param bool $list
     * @param array $additional
     * @return array
     */
	public static function provideDetail(string $constantsClass, EntityManagerInterface $em, Context $context, array $additional = []): ViewInterface;

    /**
     * @param EntityManagerInterface $em
     * @param array $additional
     * @return array
     */
	public static function provideList(string $constantsClass, EntityManagerInterface $em, Context $context, array $additional = []): ViewInterface;

    /**
     * @param EntityManagerInterface $em
     * @param array $additional
     * @return array
     */
	public static function provideAdd(string $constantsClass, EntityManagerInterface $em, Context $context): ViewInterface;
}
