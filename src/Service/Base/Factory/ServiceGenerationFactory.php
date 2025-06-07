<?php

namespace Smug\Core\Service\Base\Factory;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Psr\Container\ContainerInterface;
use Smug\Core\Entity\Connection\Query\QueryBuilder;
use Smug\Core\Service\Base\Components\Provider\DataProvider\FileContentProvider;

class ServiceGenerationFactory
{
	/**
	 * self|null
	 */
	private static $instance = null;
	
	/**
	 * ContainerInterface
	 */
	private static $myContainer;
	
	/**
	 * @param ContainerInterface $container
	 */
	public function __construct(ContainerInterface $container)
	{
		self::$myContainer = $container;
	}
	
	/**
	 * @param ContainerInterface $container
	 *
	 * @return null|ServiceGenerationFactory
	 */
	public static function init(ContainerInterface $container)
	{
		self::$myContainer = $container;
		
		if (null === self::$instance) {
			self::$instance = new self($container);
		}
		
		return self::$instance;
	}
	
    /**
     * @param string $className
     * @param mixed ...$parameters
     * @return object
     * @throws \ReflectionException
     * @throws \Exception
     */
    public static function createInstance(string $className, ...$parameters): object
    {
	    $numParams = 0;

    	if (self::hasPublicConstructor($className)) {
		    $modelConstruct = new \ReflectionMethod($className, '__construct');
		    $numParams = $modelConstruct->getNumberOfParameters();
	    }

    	if (self::isChildClass($className)) {
    		$parentClassArguments = self::getParentClassArguments($className);
		
    		if (DataHandler::getArrayLength($parentClassArguments) > 0) {
    			$parameters = DataHandler::mergeArray($parameters, $parentClassArguments);
		    }
	    }

        if (count($parameters) != $numParams && count($parameters) > 0) {
	        $tempArray = array_fill(0, $numParams, '');
	        $parameters = ($parameters + $tempArray);
        }

        if (count($parameters) > 0) {
            if (class_exists($className)) {
                $class = (new \ReflectionClass($className));
                $class->newInstanceArgs($parameters);

                return $class->newInstance(...$parameters);
            }
        } else {
	        $configs = self::getClassConfigs($className);

	        if (count($configs) > 0) {
	        	$parameters = self::getParameters($configs);

                if (class_exists($className)) {
                    $class = (new \ReflectionClass($className));
                    $class->newInstanceArgs($parameters);

                    return $class->newInstance(...$parameters);
                }
	        }
        }

        if (!class_exists($className)) {
            throw new \Exception('Class ' . $className . 'not found');
        }

        return new $className();
    }

	/**
	 * @param $class
	 * @return bool
	 */
    private static function hasPublicConstructor($class): bool {
	    try {
		    $m = new \ReflectionMethod($class, $class);
		    if ($m->isPublic()) {
			    return true;
		    }
	    } catch (\ReflectionException $e) {
	    }
	    
	    try {
		    $m = new \ReflectionMethod($class,'__construct');
		    if ($m->isPublic()) {
			    return true;
		    }
	    }
	    catch (\ReflectionException $e) {
	    }
	    
	    return false;
    }
	
	/**
	 * @param $class
	 * @return bool
	 */
    private static function isChildClass($class): bool {
    	try {
		    $m = new \ReflectionClass($class);
		    if ($m->getParentClass()) {
		        return true;
		    }
	    } catch (\ReflectionException $e) {
	    }

	    return false;
    }
	
	/**
	 * @param $class
	 * @return array
	 */
    private static function getParentClassArguments($class): array
    {
	    try {
		    $m = new \ReflectionClass($class);
		    
		    $parentClass = $m->getParentClass()->getName();
	    } catch (\ReflectionException $e) {
	    	return [];
	    }
	    
	    $configs = self::getClassConfigs($parentClass);

	    if (DataHandler::getArrayLength($configs) === 0) {
	    	return $configs;
	    }

	    return self::getParameters($configs);
    }
	
	/**
	 * @param array $configs
	 * @return array
	 */
    private static function getParameters(array $configs): array
    {
	    $parameters = [];
	    
	    foreach ($configs as $parameter) {
		    if ($parameter === 'kernel') {
			    $parameters[] = self::$myContainer->get('kernel');
			    continue;
		    }
		
		    $parameters[] = self::$myContainer->get('kernel')->getContainer()->get($parameter);
	    }
	
	    return $parameters;
    }
	
	/**
	 * @param string $className
	 * @return array
	 */
    private static function getClassConfigs(string $className): array
    {
	    $configs = [];
	
	    $configs = DataHandler::mergeArray(
		    $configs,
			FileContentProvider::getSystemFileContent('ServiceInjections.json')
	    );

	    if (DataHandler::doesKeyExists($className, $configs)) {
		    return $configs[$className];
	    }
	    
	    return [];
    }
}
