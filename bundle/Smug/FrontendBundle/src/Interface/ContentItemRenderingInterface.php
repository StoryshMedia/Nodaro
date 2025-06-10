<?php

namespace Smug\FrontendBundle\Interface;

interface ContentItemRenderingInterface {
    public static function doProcess(array $data, string $identifier): bool;
}