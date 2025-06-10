<?php

namespace Smug\FrontendBundle\Service\Route;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Symfony\Bundle\FrameworkBundle\Routing\Attribute\AsRoutingConditionService;
use Symfony\Component\HttpFoundation\Request;

#[AsRoutingConditionService(alias: 'frontend_route_checker')]
class FrontendRouteChecker
{
    public function check(Request $request): bool
    {
        if (DataHandler::isStringInString($request->getPathInfo(), '/fe/api/')) {
            return false;
        }
        if (DataHandler::isStringInString($request->getPathInfo(), '/be/api/')) {
            return false;
        }
        if (DataHandler::isStringInString($request->getPathInfo(), '/admin')) {
            return false;
        }

        return true;
    }
}
