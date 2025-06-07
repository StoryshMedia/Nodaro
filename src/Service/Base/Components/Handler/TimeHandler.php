<?php

namespace Smug\Core\Service\Base\Components\Handler;

use Smug\Core\Service\Base\Components\Provider\DataProvider\ColorProvider;
use Smug\Core\Service\Base\Components\Provider\DataProvider\TimeProvider;
use \DateTime;
use \DateInterval;
use \DatePeriod;
use DateTimeZone;
use \Exception;

/**
 * Class TimeHandler
 * @package Smug\Core\Service\Base\Components\Handler
 */
class TimeHandler
{
    public const SOLR_ISO_DATETIME_FORMAT = 'Y-m-d\TH:i:s\Z';

    /**
     * @param int $millis
     * @param bool|false $getDays
     * @return float|string
     */
    public static function getTimeFromMillis($millis, $getDays = false)
    {
        $secs = floor($millis / 1000);
        $hours = intval(($secs / 3600));
        $minutes = (($secs / 60) % 60);
        $minutes = str_pad($minutes, 2, '0', STR_PAD_LEFT);
        $seconds = $secs % 60;
        $seconds = str_pad($seconds, 2, '0', STR_PAD_LEFT);

        if ($hours >= 1) {
            $hours = str_pad($hours, 2, '0', STR_PAD_LEFT);
        } else {
            $hours = '00';
        }

        return "$hours:$minutes:$seconds";
    }

    /**
     * @param string $time
     * @return integer
     */
    public static function getMillisFromTime($time)
    {
        $millis = 0;

        $arTimes = explode(':', $time);
	
	    if (DataHandler::getArrayLength($arTimes) < 3) {
		    $arTimes = self::fillTimeParts($arTimes, DataHandler::getArrayLength($arTimes));
	    }

        $millis += (intval($arTimes[0]) * 3600) * 1000;
        $millis += (intval($arTimes[1]) * 60) * 1000;
        $millis += intval($arTimes[2]) * 1000;

        return $millis;
    }

    public static function timestampToIso(?int $timestamp = 0): string
    {
        return date(self::SOLR_ISO_DATETIME_FORMAT, $timestamp ?? 0);
    }

    public static function isoToTimestamp(string $isoTime): int
    {
        $dateTime = DateTime::createFromFormat(
            self::SOLR_ISO_DATETIME_FORMAT,
            $isoTime
        );
        return $dateTime ? (int)$dateTime->format('U') : 0;
    }

    public static function timestampToUtcIso(?int $timestamp = 0): string
    {
        return gmdate(self::SOLR_ISO_DATETIME_FORMAT, $timestamp ?? 0);
    }

    public static function utcIsoToTimestamp(string $isoTime): int
    {
        $utcTimeZone = new DateTimeZone('UTC');
        $dateTime = DateTime::createFromFormat(
            self::SOLR_ISO_DATETIME_FORMAT,
            $isoTime,
            $utcTimeZone
        );
        return $dateTime ? (int)$dateTime->format('U') : 0;
    }

    /**
     * @param string $hours
     * @return string
     */
    public static function getQuantityFromHours($hours)
    {
        $return = 0.00;
        $arTimes = explode(':', $hours);
	
	    if (DataHandler::getArrayLength($arTimes) < 3) {
		    $arTimes = self::fillTimeParts($arTimes, DataHandler::getArrayLength($arTimes));
	    }
	    
        if ($arTimes[0] !== '00') {
            $return += DataHandler::getIntFromString(
                DataHandler::removeLeadingCharacters($arTimes[0], '0')
            );
        }

        if ($arTimes[1] !== '00') {
            $return += ((DataHandler::getIntFromString(
                            DataHandler::removeLeadingCharacters($arTimes[1], '0')
                        ) * 100) / 60) / 100;
        }

        if ($arTimes[2] !== '00') {
            $return += ((DataHandler::getIntFromString(
                            DataHandler::removeLeadingCharacters($arTimes[2], '0')
                        ) * 100) / 3600) / 100;
        }

        return DataHandler::getFormattedNumber($return, 2);
    }

    public function getQuantityFromMillis($millis)
    {
        return DataHandler::getFormattedNumber($millis / 1000 / 60 / 60, 2);
    }

    /**
     * @param integer $hours
     * @return integer
     */
    public static function getMillisFromHours($hours)
    {
        $minutes = $hours * 60;
        $seconds = $minutes * 60;

        return $seconds * 1000;
    }

    /**
     * @param $millis
     * @param bool $getSeconds
     * @return false|string
     */
    public static function getHmsFromMillis($millis, $getSeconds = true)
    {
        return ($getSeconds === false) ? gmdate("H:i", $millis / 1000) : gmdate("H:i:s", $millis / 1000);
    }
	
	/**
	 * @param $time
	 * @return string
	 */
    public static function getTimeFromFloat($time)
    {
    	return sprintf('%02d:%02d', (int) $time, fmod($time, 1) * 60);
    }
	
	/**
	 * @param DateTime $start
	 * @param DateTime $end
	 * @return DatePeriod
	 */
    public static function getDatesBetweenTwoDates(DateTime $start, DateTime $end)
    {
	    return new DatePeriod(
	    	$start,
		    new DateInterval('P1D'),
		    $end
	    );
    }

    /**
     * @param integer $millis
     * @return integer
     */
    public static function getHoursFromMillis($millis)
    {
        return $millis / 60 / 60 / 1000;
    }

    /**
     * @param integer $millis
     * @return float
     */
    public static function getDaysFromMillis($millis)
    {
        return $millis / 1000 / 60 / 60 / 24;
    }

    /**
     * @param integer $millis
     * @return integer
     */
    public static function getMinutesFromMillis($millis)
    {
        return $millis / 60 / 1000;
    }

    /**
     * @param integer $millis
     * @return integer
     */
    public static function getSecondsFromMillis($millis)
    {
        return $millis / 1000;
    }

    /**
     * @param integer $minutes
     * @return integer
     */
    public static function getMillisFromMinutes($minutes)
    {
        return $minutes * 60000;
    }

    /**
     * @param integer $seconds
     * @return integer
     */
    public static function getMillisFromSeconds($seconds)
    {
        return $seconds * 1000;
    }

    /**
     * @param integer $seconds
     * @return float
     */
    public static function getDaysFromSeconds($seconds)
    {
        return $seconds / (60 * 60 * 24);
    }

    /**
     * @param integer $target
     * @param integer $actual
     * @return float
     */
    public static function getActualHourPercentage($target, $actual)
    {
        if ($target === null) {
            return 0;
        }

        if ($target === 0) {
            return $target;
        }

        if ($actual === 0) {
            return $actual;
        }

        return ($actual / $target) * 100;
    }

    /**
     * @param string $hms
     * @return int
     */
    public static function getMillisFromHms($hms)
    {
        $time = explode(':', $hms);

        if (count($time) < 3) {
            $time[] = '00';
        }
	
        list($h, $m, $s) = $time;

        $seconds = 0;
        $seconds += (intval($h) * 3600);
        $seconds += (intval($m) * 60);
        $seconds += (intval($s));

        return $seconds * 1000;
    }

    /**
     * @param $days
     * @return mixed
     */
    public static function getMillisFromDays($days)
    {
        return $days * (60 * 60 * 24 * 1000);
    }

    /**
     * @param $start
     * @param $end
     * @return mixed
     */
    public static function getRange($start, $end)
    {
        return $start - $end;
    }

    /**
     * @return int
     */
    public static function getToday()
    {
        return time();
    }

    /**
     * @param null $date
     * @return DateTime
     * @throws Exception
     */
    public static function getNewDateObject($date = null)
    {
        if ($date === null) {
            return new DateTime();
        }

        return new DateTime($date);
    }

    /**
     * @param $date
     * @param $format
     * @return false|string|null
     * @throws Exception
     */
    public static function getFormatDate($date, $format)
    {
        if ($date === null) {
            return null;
        }

        if (is_array($date)) {
            $date = self::getNewDateObject($date['date']);
        }

        if (is_string($date)) {
            $date = self::getNewDateObject($date);
        }

        return date_format($date, $format);
    }

    /**
     * @param string $string
     * @param null $add
     * @return false|int
     */
    public static function getTimeFromString(string $string, $add = null)
    {
        return ($add === null) ? strtotime($string) : strtotime($add, $string);
    }

    /**
     * @param string $date1
     * @param string $date2
     * @return boolean
     */
    public static function isDateAfterAnother($date1, $date2)
    {
        return (strtotime($date1) > strtotime($date2));
    }

    /**
     * @param $timestamp
     * @param $format
     * @return bool|string
     */
    public static function getDateFromTimestamp($timestamp, $format)
    {
	    date_default_timezone_set('Europe/Berlin');
	    return date($format, $timestamp);
    }

    /**
     * @param string $format
     * @return bool|string
     */
    public static function getDate($format)
    {
	    date_default_timezone_set('Europe/Berlin');
        return date($format);
    }

    /**
     * @param DateTime $date1
     * @param DateTime $date2
     * @return int
     */
    public static function getDaysBetweenDates(DateTime $date1, DateTime $date2)
    {
        return $date1->diff($date2)->days;
    }

    /**
     * @param DateTime $date1
     * @param DateTime $date2
     * @return bool|DateInterval
     */
    public static function getDiffBetweenDates(DateTime $date1, DateTime $date2)
    {
        return $date1->diff($date2);
    }

    /**
     * @param integer $number
     * @return string
     */
    public static function getMonthNameByNumber($number)
    {
        $number = self::getMonthIdByNumber($number);

        return TimeProvider::MONTHS[$number];
    }

    /**
     * @param integer $number
     * @return string
     */
    public static function getMonthColorByNumber($number)
    {
        $number = self::getMonthIdByNumber($number);

        return ColorProvider::COLORS[$number];
    }

    /**
     * @param $number
     * @return mixed
     */
    public static function getMonthHighlightByNumber($number)
    {
        $number = self::getMonthIdByNumber($number);

        return ColorProvider::HIGHLIGHTS[$number];
    }

    /**
     * @param string $name
     * @return boolean|integer|string
     */
    public static function getMonthNumberByName($name)
    {
        foreach (TimeProvider::MONTHS as $key => $month) {
            if ($name == $month) {
                $value = $key;
                if ($value < 10) {
                    $value = '0' . $value;
                }
                return $value;
            }
        }

        // Loop through snippet month names in case the previous loop does not match
        foreach (TimeProvider::SNIPPET_MONTHS as $key => $month) {
            if ($name == $month) {
                $value = $key;
                if ($value < 10) {
                    $value = '0' . $value;
                }
                return $value;
            }
        }

        return false;
    }

    /**
     * @param $number
     * @return mixed
     */
    public static function getMonthIdByNumber($number)
    {
        if (substr($number, 0, 1) === '0') {
            $number = str_replace('0', '', $number);
        }

        return $number;
    }

    /**
     * @param DateTime $date
     * @param $format
     * @return string
     */
    public static function getTimeStampFromDateTime(DateTime $date, $format)
    {
        return intval($date->format($format));
    }

    /**
     * @return int
     */
    public static function getTimeStamp(): int
    {
        return time();
    }

    /**
     * @param string|null $condition
     * @param string|null $dateString
     * @return DateTime
     * @throws Exception
     */
    public static function getModifiedDate($condition = null, $dateString = null)
    {
        if ($dateString !== null) {
            $date = new DateTime($dateString);
        } else {
            $date = new DateTime();
        }

        if ($condition !== null) {
            $date->modify($condition);
        }

        return $date;
    }

    /**
     * @param DateTime $date
     * @param null $condition
     * @return DateTime
     * @throws Exception
     */
    public static function modifyDateObject(DateTime $date, $condition = null)
    {
        if ($condition !== null) {
            $date->modify($condition);
        }

        return $date;
    }

    /**
     * @param string $mode
     * @param string $past
     * @return boolean|string
     * @throws Exception
     */
    public static function getLastDateName(string $mode, string $past)
    {
        switch ($mode) {
            case 'month':
                $date = self::getModifiedDate('-' . $past . ' month', 'first day of this month');
                $format = 'F';
                break;
            case 'week':
                $date = self::getModifiedDate('-' . $past . ' day');
                $format = 'l';
                break;
            case 'day':
                $date = self::getModifiedDate('-' . $past . ' day');
                $format = 'l';
                break;
            default:
                $date = self::getNewDateObject();
                $format = '';
        }

        return self::getFormatDate($date, $format);
    }

    /**
     * @param integer $hour
     * @param integer$minute
     * @return DateTime|false
     * @throws Exception
     */
    public static function getDateWithTimeSet($hour, $minute)
    {
        $date = new DateTime();

        return $date->setTime($hour, $minute);
    }

    /**
     * @param DateInterval $days
     * @return string
     */
    public static function getMonthYearFromDays(DateInterval $days)
    {
        $result = '';

        if ($days->y > 0) {
            $result .= $days->y . " Jahre ";
        }
        if ($days->m > 0) {
            $result .= $days->m . " Monate ";
        }

        $result .= $days->d . " Tage";


        return $result;
    }
	
	/**
	 * @param array $timeParts
	 * @param integer $length
	 * @return array
	 */
	private static function fillTimeParts(array $timeParts, $length): array
	{
		for ($i = $length; $i < 3; $i++) {
			$timeParts[] = '00';
		}
		
		return $timeParts;
	}
}
