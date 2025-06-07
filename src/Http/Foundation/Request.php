<?php

namespace Smug\Core\Http\Foundation;

use Smug\Core\Http\Factory\ConfigurationFactory;
use Smug\Core\Http\Factory\RouteMapperFactory;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Factory\ServiceGenerationFactory;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;

class_exists(InputBag::class);

class Request extends HttpFoundationRequest
{
    protected static array $excludedPaths = [
        '/admin/',
        '/be/api/',
        '/fe/api/',
    ];

    /**
     * Additional request parameters
     *
     * @var InputBag
     */
    public $additionalQueryParams = [];

    /**
     * @var bool
     */
    public bool $isMapped = false;

    public static function createFromGlobals(): static
    {
        $request = self::createRequestFromFactory($_GET, $_POST, [], $_COOKIE, $_FILES, $_SERVER);

        if (str_starts_with($request->headers->get('CONTENT_TYPE', ''), 'application/x-www-form-urlencoded')
            && DataHandler::isInArray(
            DataHandler::getUppercaseString(
                $request->server->get('REQUEST_METHOD', 'GET')
            ),
            ['PUT', 'DELETE', 'PATCH']
        )
        ) {
            parse_str($request->getContent(), $data);
            $request->request = new InputBag($data);
        }

        return $request;
    }

    private static function createRequestFromFactory(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null): static
    {
        if (self::$requestFactory) {
            $request = (self::$requestFactory)($query, $request, $attributes, $cookies, $files, $server, $content);
            
            if (!$request instanceof self) {
                throw new \LogicException('The Request factory must return an instance of Symfony\Component\HttpFoundation\Request.');
            }
            
            return $request;
        }

        $request = new static($query, $request, $attributes, $cookies, $files, $server, $content);
        
        $mapperFactory = ServiceGenerationFactory::createInstance(RouteMapperFactory::class);
        $routeConfigurations = ConfigurationFactory::getConfigurations($request->server->get('SERVER_NAME'));

        if (self::mapRequest($request)) {
            $mappedData = $mapperFactory->map($request->getRequestUri(), $routeConfigurations);
            
            if (!DataHandler::isEmpty($mappedData)) {
                $request->additionalQueryParams = $mappedData;
                $request->isMapped = true;
            }
        }

        return $request;
    }

    protected static function mapRequest($request): bool
    {
        foreach (self::$excludedPaths as $path) {
            if (DataHandler::isStringInString($request->getRequestUri(), $path)) 
            return false;
        }

        return true;
    }
}
