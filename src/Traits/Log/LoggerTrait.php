<?php

namespace Smug\Core\Traits\Log;

use Psr\Log\LoggerInterface;

trait LoggerTrait
{
	/**
	 * @var LoggerInterface|null
	 */
	private static ?LoggerInterface $logger = null;

    /**
     * @param LoggerInterface $logger
     */
	public function setLogger(LoggerInterface $logger)
	{
		self::$logger = $logger;
	}
	
	public static function logInfo(string $message, array $context = [])
	{
		if (self::$logger) {
			self::$logger->info($message, $context);
		}
	}
}
