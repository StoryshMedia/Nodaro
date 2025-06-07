<?php

namespace Smug\Core\Service\Base\Components\Handler;

use Composer\Autoload\ClassLoader;
use Exception;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use Smug\Core\Environment\Environment;
use Smug\Core\Service\Base\Factory\ServiceGenerationFactory;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class DataHandler
{
    /** @var string[] TAG_WHITELIST */
    const TAG_WHITELIST = [
        'h3',
        'h2',
        'h1',
        'h4',
        'h5',
        'p',
        'br',
        'b',
        'strong',
        'i',
        'em',
        's',
        'u',
        'ul',
        'ol',
        'li',
        'span',
        'table',
        'tbody',
        'tr',
        'td',
    ];

    public static function getClassesInNamespace(string $namespace, string $folder = ''): array
    {
        $loader = require __DIR__.'/../../../../../vendor/autoload.php'; // oder App-Kernel Pfad
        if (!$loader instanceof ClassLoader) {
            throw new \RuntimeException('Could not get Composer ClassLoader');
        }
    
        $prefixDirs = $loader->getPrefixesPsr4();
    
        $matchedPaths = $prefixDirs[$namespace] ?? [];
    
        $classes = [];
        foreach ($matchedPaths as $path) {
            $path .= DIRECTORY_SEPARATOR . $folder;

            if (!self::proofDir($path)) {
                continue;
            }

            $finder = new Finder();
            $finder->files()->in($path)->name('*.php');
    
            foreach ($finder as $file) {
                $relativePath = $file->getRelativePathname(); // e.g. Sub/MyClass.php
                $classNamespace = $namespace . str_replace(['/'], ['\\'], $folder);
                $className = $classNamespace . '\\' . str_replace(['/', '.php'], ['\\', ''], $relativePath);
    
                if (class_exists($className)) {
                    $classes[] = $className;
                }
            }
        }
    
        return $classes;
    }

    public static function moveFile(string $src, string $destination): bool
    {
        return rename($src, $destination);
    }

    public static function removeLeadingCharacters(string $string, string $char = " \t\n\r\0\x0B"): string
    {
        return ltrim($string, $char);
    }

    public static function removeTailingCharacters(string $string, string $char = " \t\n\r\0\x0B"): string
    {
        return rtrim($string, $char);
    }

    public static function getPositionInArray($value, array $array)
    {
        return array_search($value, array_keys($array));
    }

    public static function getPositionInMultiDimensionalArray($value, array $array, string|int $key)
    {
        return array_search($value, array_column($array, $key));
    }

    public static function removeTailingAndLeadingCharacters(string $string, string $char = " \t\n\r\0\x0B"): string
    {
        return self::removeLeadingCharacters(self::removeTailingCharacters($string, $char), $char);
    }

    public static function getUniqueId($prefix = null)
    {
        if (!$prefix) {
            return uniqid();
        } else {
            return uniqid($prefix);
        }
    }

    public static function isExtensionInstalled($name)
    {
        return extension_loaded($name);
    }

    public static function getCamelCaseString($input, $separator = '_')
    {
        return str_replace($separator, '', lcfirst(ucwords($input, $separator)));
    }

    public static function getSnakeCaseString($input, $separator = '_')
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $input));
    }

    public static function copyFile($src, $destination)
    {
        return copy($src, $destination);
    }

    public static function copyFolder($from, $to, $ext="*") {
        if (!self::proofDir($from)) {
            exit("$from does not exist"); 
        }
      
        self::makeDir($to);
      
        $all = glob("$from$ext", GLOB_MARK);
      
        if (self::getArrayLength($all) > 0) {
            foreach ($all as $a) {
                $ff = basename($a); // CURRENT FILE/FOLDER
                if (self::proofDir($a)) {
                    self::copyFolder("$from$ff/", "$to$ff/");
                } else {
                    if (!self::copyFile($a, "$to$ff")) {
                        exit("Error copying $a to $to$ff");
                    }
                }
            }
        }
    }

    public static function getArrayChunk($array, $size, $keepKeys = false)
    {
        if ($keepKeys === true) {
            return array_chunk($array, $size, true);
        } else {
            return array_chunk($array, $size);
        }
    }
	
    public static function getAddressParts(string $address)
    {
	    if ( preg_match('/([^\d]+)\s?(.+)/i', $address, $result) )
	    {
	    	return [
	    		'address' => $result[1],
			    'streetNumber' => $result[2]
		    ];
	    }
	
	    return [
		    'address' => $address,
		    'streetNumber' => '1'
	    ];
    }

    public static function getTree(array $entries, $parentId = ""): array
    {
        $tree = [];

        foreach ($entries as $entry) {
            if ($entry['parentId'] == $parentId) {
                $children = self::getTree($entries, $entry['id']);
                $entry['children'] = $children ?? [];
                $tree[] = $entry;
            }
        }

        return $tree;
    }
	
    public static function getStringFromBool($variable): string
    {
    	if (!self::isBool($variable)) {
    		return $variable;
	    }
    	
    	return ($variable === true) ? 'true' : 'false';
    }
	
    public static function getStringFromNumber($variable): string
    {
    	return strval($variable);
    }
	
    public static function getStringValue($variable): string
    {
        if (self::isNumeric($variable)) {
            return self::getStringFromNumber($variable);
        }

        if (self::isBool($variable)) {
            return self::getStringFromBool($variable);
        }

        if (self::isArray($variable)) {
            return self::getJsonEncode($variable);
        }

    	return strval($variable);
    }

    public static function getFile($file)
    {
//        if (!function_exists('curl_init')) {
        return file_get_contents($file);
//        }
//
//        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_URL, $file);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        $output = curl_exec($ch);
//        curl_close($ch);
//
//        return $output;
    }

    public static function getFileSize(string $file): int
    {
        return filesize($file);
    }

    public static function checkFile(string $file): bool
    {
//        return (file_exists($file) && is_file($file));
        return is_readable($file);
    }
	
    public static function getClassFunctions(string $class, bool $includeParentClass = true): array
    {
	    $class = new \ReflectionClass($class);
	    
	    if (self::isEmpty($class)) {
	    	return [];
	    }
	    
	    $functions = [];
	
	    foreach ($class->getMethods() as $function) {
	    	if ($includeParentClass === false && $function->class !== $class->getName()) {
	    		continue;
		    }
	    	
	    	$functions[] = [
	    		'name' => $function->getName()
		    ];
	    }
	    
    	return $functions;
    }

    public static function doesClassExist(string $className): bool
    {
        return class_exists($className);
    }

    public static function getJsonDecode($string, $add = null)
    {
        if ($add !== null) {
            $return = json_decode($string, true);
        } else {
            $return = json_decode($string);
        }

        return $return;
    }

    public static function getYamlFile(string $path)
    {
        return Yaml::parse(self::getFile($path));
    }

    public static function getFirstCapitalUpper($string)
    {
        return ucfirst($string);
    }

    public static function getUppercaseString(string $string): string
    {
        return strtoupper($string);
    }

    public static function fixWindowsFilePath(string $path): string
    {
        return str_replace(['\\', '//'], '/', $path);
    }

    public static function getJsonEncode($array, int $flags = 0)
    {
        return json_encode($array, $flags);
    }

    public static function intExplode(string $delimiter, string $string, bool $removeEmptyValues = false, int $limit = 0)
    {
        $result = explode($delimiter, $string);
        foreach ($result as $key => &$value) {
            if ($removeEmptyValues && trim($value) === '') {
                unset($result[$key]);
            } else {
                $value = (int)$value;
            }
        }
        unset($value);
        if ($limit !== 0) {
            trigger_error('The parameter $limit will be removed from ' . __METHOD__ . ' in TYPO3 v13.0.', E_USER_DEPRECATED);

            if ($limit < 0) {
                $result = array_slice($result, 0, $limit);
            } elseif (count($result) > $limit) {
                $lastElements = array_slice($result, $limit - 1);
                $result = array_slice($result, 0, $limit - 1);
                $result[] = implode($delimiter, $lastElements);
            }
        }

        return $result;
    }

    public static function callUserFunction($funcName, &$params, ?object $ref = null)
    {
        // Check if we're using a closure and invoke it directly.
        if (is_object($funcName) && is_a($funcName, \Closure::class)) {
            return call_user_func_array($funcName, [&$params, &$ref]);
        }
        $funcName = trim($funcName);
        $parts = explode('->', $funcName);
        // Call function or method
        if (count($parts) === 2) {
            // It's a class/method
            // Check if class/method exists:
            if (class_exists($parts[0])) {
                // Create object
                $classObj = ServiceGenerationFactory::createInstance($parts[0]);
                $methodName = (string)$parts[1];
                $callable = [$classObj, $methodName];
                if (is_callable($callable)) {
                    // Call method:
                    $content = call_user_func_array($callable, [&$params, &$ref]);
                } else {
                    throw new \InvalidArgumentException('No method name \'' . $parts[1] . '\' in class ' . $parts[0], 1294585865);
                }
            } else {
                throw new \InvalidArgumentException('No class named ' . $parts[0], 1294585866);
            }
        } elseif (function_exists($funcName) && is_callable($funcName)) {
            // It's a function
            $content = call_user_func_array($funcName, [&$params, &$ref]);
        } else {
            // Usually this will be annotated by static code analysis tools, but there's no native "not empty string" type
            throw new \InvalidArgumentException('No function named: ' . $funcName, 1294585867);
        }
        return $content;
    }

    public static function basename(string $path): string
    {
        $targetLocale = $GLOBALS['systemLocale'] ?? '';
        if (empty($targetLocale)) {
            return basename($path);
        }
        $currentLocale = (string)setlocale(LC_CTYPE, '0');
        setlocale(LC_CTYPE, $targetLocale);
        $basename = basename($path);
        setlocale(LC_CTYPE, $currentLocale);
        return $basename;
    }

    public static function getCanonicalPath(string $path): string
    {
        // Replace backslashes with slashes to work with Windows paths if given
        $path = trim(str_replace('\\', '/', $path));

        // @todo do we really need this? Probably only in testing context for vfs?
        $protocol = '';
        if (str_contains($path, '://')) {
            [$protocol, $path] = explode('://', $path);
            $protocol .= '://';
        }

        $absolutePathPrefix = '';
        if (static::isAbsolutePath($path)) {
            if (Environment::isWindows() && substr($path, 1, 2) === ':/') {
                $absolutePathPrefix = substr($path, 0, 3);
                $path = substr($path, 3);
            } else {
                $path = ltrim($path, '/');
                $absolutePathPrefix = '/';
            }
        }

        $theDirParts = explode('/', $path);
        $theDirPartsCount = count($theDirParts);
        // This cannot use a foreach() as some steps skip ahead multiple elements.
        for ($partCount = 0; $partCount < $theDirPartsCount; $partCount++) {
            // double-slashes in path: remove element
            if ($theDirParts[$partCount] === '') {
                array_splice($theDirParts, $partCount, 1);
                $partCount--;
                $theDirPartsCount--;
            }
            // "." in path: remove element
            if (($theDirParts[$partCount] ?? '') === '.') {
                array_splice($theDirParts, $partCount, 1);
                $partCount--;
                $theDirPartsCount--;
            }
            // ".." in path:
            if (($theDirParts[$partCount] ?? '') === '..') {
                if ($partCount >= 1) {
                    // Remove this and previous element
                    array_splice($theDirParts, $partCount - 1, 2);
                    $partCount -= 2;
                    $theDirPartsCount -= 2;
                } elseif ($absolutePathPrefix) {
                    // can't go higher than root dir
                    // simply remove this part and continue
                    array_splice($theDirParts, $partCount, 1);
                    $partCount--;
                    $theDirPartsCount--;
                }
            }
        }

        return $protocol . $absolutePathPrefix . implode('/', $theDirParts);
    }

    public static function fixPermissions($path, $recursive = false)
    {
        $targetPermissions = null;
        if (Environment::isWindows()) {
            return true;
        }
        $result = false;
        // Make path absolute
        if (!static::isAbsolutePath($path)) {
            $path = static::getFileAbsFileName($path);
        }
        if (static::isAllowedAbsPath($path)) {
            if (@is_file($path)) {
                $targetPermissions = '0644';
            } elseif (@is_dir($path)) {
                $targetPermissions = '0755';
            }
            if (!empty($targetPermissions)) {
                // make sure it's always 4 digits
                $targetPermissions = str_pad($targetPermissions, 4, '0', STR_PAD_LEFT);
                $targetPermissions = octdec($targetPermissions);
                // "@" is there because file is not necessarily OWNED by the user
                $result = @chmod($path, (int)$targetPermissions);
            }
            
            if ($recursive && @is_dir($path)) {
                $handle = opendir($path);
                if (is_resource($handle)) {
                    while (($file = readdir($handle)) !== false) {
                        $recursionResult = null;
                        if ($file !== '.' && $file !== '..') {
                            if (@is_file($path . '/' . $file)) {
                                $recursionResult = static::fixPermissions($path . '/' . $file);
                            } elseif (@is_dir($path . '/' . $file)) {
                                $recursionResult = static::fixPermissions($path . '/' . $file, true);
                            }
                            if (isset($recursionResult) && !$recursionResult) {
                                $result = false;
                            }
                        }
                    }
                    closedir($handle);
                }
            }
        }
        return $result;
    }

    public static function isAbsolutePath(string $path): bool
    {
        if (Environment::isWindows() && (substr($path, 1, 2) === ':/' || substr($path, 1, 2) === ':\\')) {
            return true;
        }
        
        return str_starts_with($path, '/') || str_starts_with($path, 'vfs://');
    }

    public static function isAllowedAbsPath($path)
    {
        if (substr($path, 0, 6) === 'vfs://') {
            return true;
        }
        return static::isAbsolutePath($path) && static::validPathStr($path)
            && (
                str_starts_with($path, Environment::getProjectPath())
                || str_starts_with($path, Environment::getPublicPath())
                || static::isAllowedAdditionalPath($path)
            );
    }

    public static function isAllowedAdditionalPath(string $path): bool
    {
        $path = self::sanitizeTrailingSeparator($path);
        $allowedPaths = [];
        if (is_string($allowedPaths)) {
            $allowedPaths = [$allowedPaths];
        }
        if (!is_array($allowedPaths)) {
            throw new Exception('is expected to be an array.', 1707408379);
        }
        foreach ($allowedPaths as $allowedPath) {
            $allowedPath = trim($allowedPath);
            if ($allowedPath !== '' && str_starts_with($path, self::sanitizeTrailingSeparator($allowedPath))) {
                return true;
            }
        }
        return false;
    }

    public static function sanitizeTrailingSeparator(string $path, string $separator = '/'): string
    {
        return rtrim($path, $separator) . $separator;
    }

    public static function validPathStr($theFile)
    {
        return !str_contains($theFile, '//') && !str_contains($theFile, '\\')
            && preg_match('#(?:^\\.\\.|/\\.\\./|[[:cntrl:]])#u', $theFile) === 0;
    }

    public static function getFileAbsFileName($filename)
    {
        $fileName = (string)$filename;
        if ($fileName === '') {
            return '';
        }
        $checkForBackPath = fn(string $fileName): string => $fileName !== '' && static::validPathStr($fileName) ? $fileName : '';

        if (static::isAbsolutePath($fileName)) {
            if (str_starts_with($fileName, Environment::getProjectPath()) ||
                str_starts_with($fileName, Environment::getPublicPath())) {
                return $checkForBackPath($fileName);
            }
            return '';
        }

        $fileName = Environment::getPublicPath() . '/' . $fileName;
        return $checkForBackPath($fileName);
    }

    public static function getLinkPartsFromClassString(string $class)
    {
        $array = self::explodeArray('\\', $class);

        return [
            'namespace' => $array[0],
            'bundle' => self::getReplaceString('Bundle', '', $array[1]),
            'model' => self::getLastArrayElement($array)
        ];
    }

    public static function mergeArray(array $array1, array $array2)
    {
        return array_merge($array1, $array2);
    }

    public static function mergeArrayRecursive(array $array1, array $array2)
    {
        return array_merge_recursive($array1, $array2);
    }

    public static function stripString(string $string): string
    {
        return strip_tags($string, self::TAG_WHITELIST);
    }

    public static function truncateString(string $string, int $length, string $suffix = ''): string
    {
        $result = strlen($string) > $length ? mb_substr($string, 0, $length) . "..." : $string;

        return (self::isEmpty($suffix)) ? $result : $result . $suffix;
    }

    public static function doesKeyExists($key, array $array)
    {
        return array_key_exists($key, $array);
    }

    public static function searchInMultiArray(array $array, string $key, mixed $value)
    {
        $outputArray = [];
        $arrIt = new RecursiveIteratorIterator(
            new RecursiveArrayIterator($array)
        );

        foreach ($arrIt as $sub) {
            $subArray = $arrIt->getSubIterator();
            if ($subArray[$key] === $value) {
                $outputArray[] = iterator_to_array($subArray);
            }
        }

        return (self::getArrayLength($outputArray) > 0);
    }

    public static function isNull($value)
    {
        return is_null($value);
    }

    public static function isFloat($value)
    {
        return is_float($value);
    }

    public static function isInteger($value)
    {
        if ($value === '' || is_object($value) || is_array($value)) {
            return false;
        }

        return is_integer($value);
    }
	
    public static function isInstanceOf($value, $class)
    {
        return $value instanceof $class;
    }

    public static function getArrayKeys(array $array)
    {
        return array_keys($array);
    }

    public static function getLastArrayElement($array)
    {
        return array_pop($array);
    }

    public static function encodeArray(array $data): string
    {
        return base64_encode(serialize($data));
    }

    public static function getArrayWithoutLastArrayElement(array $array): array
    {
        unset($array[count($array)-1]);

        return $array;
    }

    public static function getStringOccurrencesInString(string $string, string $searchString)
    {
        return substr_count($string, $searchString);
    }

    public static function isCharInString($char, $string)
    {
        return strpos($string, $char);
    }

    public static function getStringPosition(string $char, string $string): string
    {
        return strpos($string, $char);
    }

    public static function isStringInString($haystack, $needle)
    {
        return stristr($haystack, $needle);
    }

    public static function isSet($variable)
    {
        return isset($variable);
    }

    public static function isArray($value)
    {
        return is_array($value);
    }

    public static function arrayExists($key, $value)
    {
        if (!self::isArray($value)) {
            return false;
        }

        return (self::doesKeyExists($key, $value) || (self::isArray($value[$key]) && self::getArrayLength($value[$key]) > 0));
    }

    public static function isEmpty($value)
    {
        return empty($value);
    }

    public static function isBool($value)
    {
        return is_bool($value);
    }

    public static function isString($variable)
    {
        return is_string($variable);
    }

    public static function isObject($obj)
    {
        return is_object($obj);
    }

    public static function getClassName($obj)
    {
        return get_class($obj);
    }

    public static function getLowerString($string)
    {
        return strtolower($string);
    }

    public static function getReplaceString($search, $replace, $string)
    {
        return str_replace($search, $replace, $string);
    }

    public static function trimString(string $string): string
    {
        return trim($string);
    }

    public static function groupByField(array $values, string $field): array
    {
        $result = [];
        
        foreach ($values as $value) {
            if (!isset($value[$field])) {
                continue;
            }

            $result[$value[$field]][] = $value;
        }
        return $result;
    }

    public static function getPregReplaceString($search, $replace, $string)
    {
        return preg_replace($search, $replace, $string);
    }

    public static function checkFilterVars($data, $check)
    {
        return filter_var($data, $check);
    }

    public static function getSerialize(array $array)
    {
        return serialize($array);
    }

    public static function isNumeric($value)
    {
        return is_numeric($value);
    }
	
    public static function doesMethodExist($object, string $method)
    {
        return method_exists($object, $method);
    }
	
    public static function doesPropertyExist($object, string $property)
    {
        return property_exists($object, $property);
    }

    public static function getUnSerialize($string)
    {
        return unserialize($string);
    }

    public static function unsetArrayElement(array $array, $element)
    {
        if (!self::doesKeyExists($element, $array)) {
            return $array;
        }

        foreach ($array as $key => $value) {
            if ($key === $element) {
                unset($array[$key]);
            }
        }

        return $array;
    }

    public static function unsetArrayElementByValue(array $array, $value)
    {
        if (($key = array_search($value, $array)) !== false) {
            unset($array[$key]);
        }

        return $array;
    }

    public static function getIntFromString($string)
    {
        if ($string === null) {
            return 0;
        }

        if (self::isInteger($string)) {
            return $string;
        }

        return intval($string);
    }

    public static function getFloatFromString($string)
    {
        return floatval(str_replace(' ', '', str_replace(',', '.', $string)));
    }

    public static function getFormattedNumber($number, $decimals = 0 , $dec_point = '.' , $thousands_sep = ' ')
    {
        return number_format($number, $decimals, $dec_point, $thousands_sep);
    }

    public static function revertFormattedNumber($number, string $decPoint = null) {
        if (empty($decPoint)) {
            $locale = localeconv();
            $decPoint = $locale['decimal_point'];
        }

        return floatval(str_replace($decPoint, '.', preg_replace('/[^\d'.preg_quote($decPoint).']/', '', $number)));
    }

    public static function getNumberWithDecimals($number, $int)
    {
        return number_format((float)$number, $int, '.', '');
    }

    public static function roundNumber($number, $digests = 2)
    {
        return round($number, $digests);
    }

    public static function proofDir(string $dir): bool
    {
        return is_dir($dir);
    }

    public static function makeDir(string $dir): bool
    {
        if (!self::proofDir($dir)) {
            return mkdir($dir);
        }

        return false;
    }

    public static function removeDir(string $dir): bool
    {
        if (self::proofDir($dir)) {
            return rmdir($dir);
        }

        return false;
    }
    
    public static function hasProtocolAndScheme(string $path): bool
    {
        return str_starts_with($path, '//') || strpos($path, '://') > 0;
    }
    
    public static function stringStartsWith(string $string, string $check): bool
    {
        return str_starts_with($string, $check);
    }

    public static function removeEmptyArrayElements(array $array): array
    {
        return array_filter($array, fn($value) => !is_null($value) && $value !== '');
    }

    public static function getStringLength($string)
    {
        return strlen($string);
    }

    public static function getRandomPosition($start, $end)
    {
        return rand($start, $end);
    }

    public static function generateRandomString($length = 10) {
        return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
    }

    public static function getTextWithLineBreaks($string)
    {
        return nl2br(htmlentities($string, ENT_QUOTES, 'UTF-8'));
    }

    public static function getVariableType($variable): string
    {
        return gettype($variable);
    }

    public static function getTextAsHTML($string)
    {
        return htmlentities($string, ENT_QUOTES, 'UTF-8');
    }

    public static function getMd5Hash($string)
    {
        return md5($string);
    }

    public static function getArrayLength($array)
    {
        if ($array === null) {
            return 0;
        }
        
        return count($array);
    }

    public static function getArrayValues(array $array)
    {
        return array_values($array);
    }

    public static function getArraySplice(array $array, int $offset, int $length)
    {
        return array_splice($array, $offset, $length);
    }

    public static function getArrayUnShift(array $array, $var): array
    {
        array_unshift($array, $var);

        return $array;
    }

    public static function pushToArray(array $array, $var)
    {
        return array_push($array, $var);
    }

    public static function doesPregMatch($condition, $string)
    {
        return preg_match($condition, $string);
    }

    public static function explodeArray(string $delimiter, string $string): array
    {
        return explode($delimiter, $string);
    }

    public static function implodeArray(string $separator, array $array): string
    {
        return implode($separator, $array);
    }

    public static function scanDir($dir)
    {
        return scandir($dir);
    }

    public static function getSizeOf($array)
    {
        return sizeof($array);
    }

    public static function isInArray($string, $array)
    {
        return in_array($string, $array);
    }

    public static function isElementInArray($string, $array)
    {
        return in_array($string, $array);
    }

    public static function deleteObject($obj)
    {
        return unlink($obj);
    }

    public static function getSubString($string, $position, $position2 = null)
    {
        if ($position2 !== null) {
            return mb_substr($string, $position, $position2);
        } else {
            return mb_substr($string, $position);
        }
    }

    public static function getExternalXmlFile($src)
    {
        return simplexml_load_file($src);
    }

    public static function sortItemsByField($items, $field, ?string $direction = null)
    {
        $return = [];

        foreach ($items as $item) {
            $return[$item[$field]] = $item;
        }

        if ($direction === null) {
            ksort($return);
        } else {
            if ($direction === 'ASC') {
                ksort($return);
            } else if ($direction === 'DESC') {
                krsort($return);
            }
        }

        return $return;
    }

    public static function simpleSortArray(array $array): array
    {
        asort($array);

        return $array;
    }

    public static function simpleSortArrayByKey(array $array): array
    {
        ksort($array);

        return $array;
    }

    public static function getFirstArrayElement(array $array)
    {
    	if (self::getArrayLength($array) === 0) {
    		return $array;
	    }
    	
        return array_values($array)[0];
    }


    public static function getFirstArrayKey(array $array)
    {
    	if (self::getArrayLength($array) === 0) {
    		return '';
	    }
    	
        return array_key_first($array);
    }

    public static function getRangeOfArrayElements(array $array, int $start, int $range)
    {
        return array_slice($array, $start, $range);
    }

    public static function removeFirstArrayElement(array $array)
    {
        array_shift($array);

        return $array;
    }

    public static function validateJson(string|array|int|float $value)
    {
        if (!self::isString($value)) {
            return false;
        }

        return json_validate($value);
    }

    public static function getFilePathParts($path)
    {
        $arParts = self::explodeArray('.', $path);
        $filename = substr(strrchr($arParts[0], '/'), 1);

        return [
            'extension' => $arParts[1],
            'file' => $filename,
            'path' => self::getReplaceString($filename, '', $arParts[0])
        ];
    }

    public static function getFileLocationPath($path)
    {
        return pathinfo($path, PATHINFO_DIRNAME);;
    }

    public static function getFileNameWithoutExtension(string $file)
    {
        $arParts = self::explodeArray(".", $file);

        return $arParts[0];
    }

    public static function getFileExtension(string $file)
    {
        $arParts = self::explodeArray(".", $file);

        return $arParts[1];
    }

    public static function getLastCharacterFromString($string)
    {
        return substr($string, -1);
    }

    public static function doesFileExist(string $path): bool
    {
        return file_exists($path);
    }
    
    public static function trimExplode($delim, $string, $removeEmptyValues = false, $limit = 0): array
    {
        $result = explode($delim, (string)$string);
        if ($removeEmptyValues) {
            $result = array_values(array_filter($result, static fn(string $item): bool => trim($item) !== ''));
        }

        if ($limit === 0) {
            return array_map(trim(...), $result);
        }

        if ($limit < 0) {
            return array_map(trim(...), array_slice($result, 0, $limit));
        }

        $tail = array_slice($result, $limit - 1);
        $result = array_slice($result, 0, $limit - 1);
        if ($tail) {
            $result[] = implode($delim, $tail);
        }
        return array_map(trim(...), $result);
    }

    public static function getFileParts($file)
    {
        $arParts = self::explodeArray(".", $file);

        return [
            'extension' => $arParts[1],
            'file' => $arParts[0]
        ];
    }

    public static function getBooleanValue($value)
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false;
    }

    public static function writeFile(string $path, string $content)
    {
        $fp = fopen($path, 'w+');
        fputs($fp, $content);
        fclose($fp);
    }

    public static function deleteFile(string $path)
    {
        unlink($path);
    }

    public static function getObjectVariables($obj)
    {
        return get_object_vars($obj);
    }

    public static function convertObjectToArray($array)
    {
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    $array[$key] = self::convertObjectToArray($value);
                }
                if ($value instanceof \stdClass) {
                    $array[$key] = self::convertObjectToArray((array)$value);
                }
            }
        }

        if ($array instanceof \stdClass) {
            return self::convertObjectToArray((array)$array);
        }

        return $array;
    }
}
