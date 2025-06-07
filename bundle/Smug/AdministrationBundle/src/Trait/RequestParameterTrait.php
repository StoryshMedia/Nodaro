<?php

namespace Smug\AdministrationBundle\Trait;

use Smug\Core\Http\Foundation\Request;
use Smug\Core\Service\Base\Components\Handler\DataHandler;

trait RequestParameterTrait
{
    public function getSlugParts(Request $request): array
    {
        $urlPaths = self::getUrlPaths($request);

        return [
            'get' => $_GET,
            'site' => $urlPaths['site'],
            'item' => $urlPaths['item'],
            'additional' => $request->additionalQueryParams
        ];
    }

    protected static function getUrlPaths(Request $request): array
    {
        $urlWithoutGet  = DataHandler::getPregReplaceString('/\\?.*/', '', $request->getPathInfo());
        $itemSlug = '';
        $site = $urlWithoutGet;

        if ($request->isMapped) {
            $parts = DataHandler::explodeArray('/', $urlWithoutGet);
            $itemSlug = DataHandler::getLastArrayElement($parts);
            array_pop($parts);
            $site = DataHandler::implodeArray('/', $parts);

            if (DataHandler::getLastCharacterFromString($site) !== '/') {
                $site = $site . '/';
            }
        }

        return [
            'item' => $itemSlug,
            'site' => $site
        ];
    }
}