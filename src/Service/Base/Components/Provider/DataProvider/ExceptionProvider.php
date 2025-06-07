<?php

namespace Smug\Core\Service\Base\Components\Provider\DataProvider;

use Smug\Core\Traits\Log\LoggerTrait;

class ExceptionProvider
{
    public static function getException($exception)
    {
    	$info = [
		    'success' => false,
		    'code' => ($exception->getCode() < 100 || $exception->getCode() > 599 || $exception->getCode() === 403) ? 500 : $exception->getCode(),
		    'line' => $exception->getLine(),
		    'file' => $exception->getFile(),
		    'message' => $exception->getMessage(),
	    ];
    	
	    LoggerTrait::logInfo($exception->getMessage(), $info);
	
	    return $info;
    }
}
