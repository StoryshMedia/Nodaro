<?php

namespace Smug\Core\Service\Base\Components\Handler;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class RequestHandler
 * @package Smug\Core\Service\Base\Components\Handler
 */
class RequestHandler
{
	/**
	 * @param Request $request
	 * @param bool $isFeRequest
	 * @return array|mixed
	 */
    public static function getRequestData(Request $request, bool $isFeRequest = false): array
    {
        if (!$isFeRequest) {
            $data = $request->get('data', '');

            if ($data === '') {
                $data = $request->get('$data', '');

                if ($data === '') {
                    return [];
                }
            }

	        $params = json_decode($data);
        } else {
            $data = DataHandler::getJsonDecode($request->getContent(), true);

            if (!DataHandler::isArray($data)) {
                return [];
            }

	        return $data['data'];
        }

        if (DataHandler::isArray($params)) {
            $return = [];

            foreach ($params as $param) {
                $return[] = DataHandler::convertObjectToArray(
                    DataHandler::getObjectVariables($param)
                );
            }

            return $return;
        }

        $data = DataHandler::convertObjectToArray(
            DataHandler::getObjectVariables($params)
        );

        return $data;
    }
}
