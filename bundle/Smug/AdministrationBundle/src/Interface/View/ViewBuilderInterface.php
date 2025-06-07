<?php

namespace Smug\AdministrationBundle\Interface\View;

use Smug\Core\Context\Context;

interface ViewBuilderInterface {
    public static function build(array $configuration, Context $context): ViewInterface;
}