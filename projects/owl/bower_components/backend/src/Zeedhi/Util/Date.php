<?php
/**
 * This file contains the class of utilities for Date and DateTime objects in php.
 *
 * PHP Version 5.3.3
 *
 * @category PHP
 * @package  Controller
 * @author   Paulo Pereira <paulo.pereira@teknisa.com>
 * @license  http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version  SVN: $Id: coding-standard-tutorial.xml,v 1.9 2008-10-09 15:16:47 Exp $
 * @link     http://pear.php.net/package/PHP_CodeSniffer
 */


namespace Zeedhi\Util;

use \DateTime, DateInterval;


/**
 * Class Date
 *
 * This class provides some useful functions to work with data.
 *
 * @package Zeedhi\Util
 */
class Date
{

    /**
     * Constant used in functions that uses days as base to calculate over dates.
     */
    const DAY         = "D";
    /**
     * Constant used in functions that uses months as base to calculate over dates.
     */
    const MONTH       = "M";
    /**
     * Constant used in functions that uses years as base to calculate over dates.
     */
    const YEAR        = "Y";
    /**
     * Constant used in functions that uses weeks as base to calculate over dates.
     */
    const WEEK        = "W";
    /**
     * Constant used in functions that uses hours as base to calculate over dates.
     */
    const HOURS       = "H";
    /**
     * Constant used in functions that uses minutes as base to calculate over dates.
     */
    const MINUTES     = "m";
    /**
     * Constant used in functions that uses seconds as base to calculate over dates.
     */
    const SECONDS     = "S";
    /**
     * Constant used in functions that uses days of week as base to calculate over dates.
     */
    const DAY_OF_WEEK  = "N";
    /**
     * Constant used in functions that uses days of months as base to calculate over dates.
     */
    const DAY_OF_MONTH = "d";

    /**
     * Constant used in comparative functions. This one determines that this function will work to find a date before
     * other.
     */
    const BEFORE       = 1;
    /**
     * Constant used in comparative functions. This one determines that this function will work to find a date after
     * other.
     */
    const AFTER        = 2;
    /**
     * Constant used in comparative functions. This one is used in functions to verify if dates are equal.
     */
    const EQUALS       = 3;

    /*@todo verify how will the formats (like FORMATO_BRASILEIRO will work here.)*/
    /* @todo  Define default format (Or the method to get it).*/
    const DEFAULT_FORMAT          = "d/m/Y";
    const DEFAULT_DATETIME_FORMAT = "d/m/Y H:i:s";

    /**
     * This functions below are used to calculate over hours.
     */
    const HOUR_MINUTE_SECONDS = 1;
    const HOUR_MINUTES        = 2;

    /**
     * String pattern used by DateTime Objects.
     */
    const DATE_TIME_PATTERN   = "/^\d\d\/\d\d\/\d\d\d\d \d\d\:\d\d\:\d\d$/";
    /**
     * String pattern used by Date Objects.
     */
    const DATE_PATTERN        = "/^\d\d\/\d\d\/\d\d\d\d$/";

    /**
     * Adds an interval to a date, both, date and interval, passed by parameters. The interval module (e.g. day,
     * month, hour) will also be determined by a parameter.
     *
     * @param DateTime $date           Date object that will have its value incremented. Although this is a date
     *                                 object, its original value will not be changed by this method. Only the DateTime
     *                                 object returned by this method will be changed.
     * @param int      $interval       Determines which interval will be added. This value must be a natural and
     *                                 positive (greater than zero) value.
     * @param string   $intervalModule Determines which type of interval will be incremented to the date. The values
     *                                 of the constants DAY, MONTH, YEAR, WEEK, HOURS, MINUTES, SECONDS are accepted
     *                                 here. By default, the module to be incremented is DAY.
     *
     * @return DateTime The original date incremented by a determined interval in a determined module of time.
     */
    public static function addInterval(DateTime $date, $interval, $intervalModule = self::DAY)
    {
        $dateReturn = clone $date;
        return $dateReturn->add(new \DateInterval("P$interval$intervalModule"));
    }

    /**
     * Subtracts an interval to a date, both, date and interval, passed by parameters. The interval module (e.g. day,
     * month, hour) will also be determined by a parameter.
     *
     * @param DateTime $date            Date object that will have its value decremented. Although this is a date
     *                                 object, its original value will not be changed by this method. Only the DateTime
     *                                 object returned by this method will be changed.
     * @param int      $interval       Determines which interval will be subtracted. This value must be a natural and
     *                                 positive (greater than zero) value.
     * @param string   $intervalModule Determines which type of interval will be decremented to the date. The values
     *                                 of the constants DAY, MONTH, YEAR, WEEK, HOURS, MINUTES, SECONDS are accepted
     *                                 here. By default, the module to be incremented is DAY.
     *
     * @return DateTime The original date decremented by a determined interval in a determined module of time.
     */
    public static function subtractInterval(DateTime $date, $interval, $intervalModule = self::DAY)
    {
        $dateReturn = clone $date;
        return $dateReturn->sub(new \DateInterval("P$interval$intervalModule"));
    }

    /**
     * Compares two DateTime objects, verifying if they are equals or if one is before other, in a time line. The way
     * that these dates will be compared (if the method will calculate if they are equals, if the first one or the
     * second one is earlier) depends of the third parameter, that will receive the value of one of the following
     * constants: BEFORE, AFTER, EQUALS. This parameter will aways create the rule "The left date(first date parameter)
     * is BEFORE / AFTER / EQUALS the right date(the second parameter)".
     *
     *
     * @param DateTime $leftDate   The first date of the comparison.
     * @param DateTime $rightDate  The second date of the comparison.
     * @param int      $comparison The way that these two dates will be compared. This parameter can have as parameter
     *                             the values of the constants BEFORE, AFTER and EQUALS. As said above, this parameter
     *                             always means "The left date is BEFORE / AFTER / EQUALS the right date".
     *
     * @return null
     */
    public static function compareDates(DateTime $leftDate, DateTime $rightDate, $comparison = self::BEFORE)
    {
        if ($comparison === self::BEFORE) {
            $result = self::getIntervalInSeconds($leftDate, $rightDate) > 0;
        } else if ($comparison === self::AFTER) {
            $result = self::getIntervalInSeconds($leftDate, $rightDate) < 0;
        } else {
            $result = self::getIntervalInSeconds($leftDate, $rightDate) === 0;
        }
        return $result;
    }

    /**
     * Returns the first day of the month.
     *
     * @param DateTime $date Any date of that represents a month and an year.
     *
     * @return DateTime Returns the first day the month and year set by the parameter $date.
     * @throws \Exception
     */
    public static function getFirstDayOfMonth(DateTime $date)
    {
        try {
            $month = $date->format("m");
            $year = $date->format("Y");
            // Get the first day of the month.
            $dateReturn = date(self::DEFAULT_FORMAT, mktime(0, 0, 0, $month, 1, $year));

            return self::getDateFromString($dateReturn);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Returns the last day of the month.
     *
     * @param DateTime $date Any date of that represents a month and an year.
     *
     * @return DateTime Returns the first day the month and year set by the parameter $date.
     * @throws \Exception
     */
    public static function getLastDayOfMonth(DateTime $date)
    {
        try {
            $month      = $date->format("m");
            $year       = $date->format("Y");
            // Get the last day of the month.
            $dateReturn = date("t/m/Y", mktime(0, 0, 0, $month, 1, $year));

            return self::getDateFromString($dateReturn);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Returns a DateTime Object representing the date passed (in the string format) in parameter. In this method it's
     * also possible to set the default format of date, represented by DEFAULT_FORMAT constant.
     *
     * @param string $date     The date (in string format) to be converted to DateTime object.
     * @param string $format   String's date's format. By default, its value is DEFAULT_FORMAT.
     * @param bool   $truncate Verify if is necessary to truncate the date before return it. Truncated data has hours,
     *                         minutes and seconds equals zero.
     *
     * @return DateTime
     * @throws \Exception
     */
    public static function getDateFromString($date, $format = self::DEFAULT_FORMAT, $truncate = false)
    {
        try {
            $date = DateTime::createFromFormat($format, $date);
            if ($truncate) {
                $date = self::truncateData($date);
            }

            return $date;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Return an array of date objects between the initial date (inclusive) and the final date
     *
     * @param DateTime $initialDate
     * @param DateTime $finalDate
     *
     * @throws \Exception It will throw an exception if the initial date is later than the final date.
     * @return array
     */
    public static function getDatesBetween(DateTime $initialDate, DateTime $finalDate)
    {
        if (self::compareDates($initialDate, $finalDate, self::AFTER)) {
            throw new \Exception("The initial date must be earlier than the final date.");
        }

        $interval = $initialDate->diff($finalDate)->days;
        $dates          = array();

        for ($i = 0; $i <= $interval; $i++) {
            $auxData = clone $initialDate;
            $dates[] = $auxData->add(new \DateInterval("P".$i.self::DAY));
        }

        return $dates;
    }

    /**
     * Return the actual date and hour. Optionally, this method can also return the date truncated.
     *
     * @param bool $truncate If truncate is equals true, then this method will return the object with hour, minutes and
     *                       seconds equals zero.
     *
     * @return DateTime Return the value of now.
     */
    public static function now($truncate = false)
    {
        $now = new DateTime();
        return $truncate ? self::truncateData($now) : $now;
    }

    /**
     * Return the greater date between left and right dates.
     *
     * @param string|\DateTime $leftDate  Date to be compared. If this one is greater, than this will be returned.
     * @param string|\DateTime $rightDate Date to be compared. If this one is greater, than this will be returned.
     *
     * @return mixed
     */
    public static function getGreater($leftDate, $rightDate)
    {
        if (self::getIntervalInSeconds($leftDate, $rightDate) < 0) {
            return $leftDate;
        } else {
            return $rightDate;
        }
    }

    /**
     * Return the lesser date between left and right dates.
     *
     * @param string|\DateTime $leftDate  Date to be compared. If this one is lesser, than this will be returned.
     * @param string|\DateTime $rightDate Date to be compared. If this one is lesser, than this will be returned.
     *
     * @return mixed
     */
    public static function getLesser($leftDate, $rightDate)
    {
        if (self::getIntervalInSeconds($leftDate, $rightDate) > 0) {
            return $leftDate;
        } else {
            return $rightDate;
        }
    }

    /**
     * Return the day of the date (this date is passed as parameter). This parameter can be string or DateTime object.
     *
     * @param DateTime|string $date   Date.
     * @param string          $format Format of the string. It has a default value equals DEFAULT_FORMAT.
     *
     * @return mixed
     * @throws \Exception
     */
    public static function day($date, $format = self::DEFAULT_FORMAT)
    {
        if (($date instanceof DateTime) === false) {
            try {
                $date = self::getDateFromString($date, $format);
            } catch (\Exception $e) {
                throw $e;
            }
        }

        if ((bool)$date == false) {
            return null;
        }

        return $date->format("d");
    }

    /**
     * Return the month of the date (this date is passed as parameter). This parameter can be string or DateTime object.
     *
     * @param DateTime|string $date   Date.
     * @param string          $format Format of the string. It has a default value equals DEFAULT_FORMAT.
     *
     * @return mixed
     * @throws \Exception
     */
    public static function month($date, $format = self::DEFAULT_FORMAT)
    {
        if (($date instanceof DateTime) === false) {
            try {
                $date = self::getDateFromString($date, $format);
            } catch (\Exception $e) {
                throw $e;
            }
        }

        if ((bool)$date == false) {
            return null;
        }

        return $date->format("m");
    }

    /**
     * Return the year of the date (this date is passed as parameter). This parameter can be string or DateTime object.
     *
     * @param DateTime|string $date   Date.
     * @param string          $format Format of the string. It has a default value equals DEFAULT_FORMAT.
     *
     * @return mixed
     * @throws \Exception
     */
    public static function year($date, $format = self::DEFAULT_FORMAT)
    {
        if (($date instanceof DateTime) === false) {
            try {
                $date = self::getDateFromString($date, $format);
            } catch (\Exception $e) {
                throw $e;
            }
        }

        if ((bool)$date == false) {
            return null;
        }

        return $date->format("Y");
    }

    /**
     * Return the quantity of days between to dates (passed as parameters).
     *
     * @param DateTime|string $initialDate Initial date.
     * @param DateTime|string $finalDate   Final date.
     *
     * @throws \Exception
     * @return int
     */
    public static function getQtdeBetweenDays($initialDate, $finalDate)
    {

        if (self::compareDates($initialDate, $finalDate, self::AFTER)) {
            throw new \Exception("The initial date must be earlier than the final date.");
        }

        if (($initialDate instanceof DateTime) === false) {
            $initialDate = self::getDateFromString($initialDate);
        }

        if (($finalDate instanceof DateTime) === false) {
            $finalDate = self::getDateFromString($finalDate);
        }

        /**
         * @var $initialDate DateTime
         * @var $finalDate DateTime
         * @var $interval \DateInterval
         */
        $interval = $initialDate->diff($finalDate);
        return (int)$interval->format('%R%a');
    }

    /**
     * Return the quantity of days between to dates (passed as parameters).
     *
     * @param DateTime|string $initialDate Initial date.
     * @param DateTime|string $finalDate   Final date.
     *
     * @throws \Exception
     * @return int
     */
    public static function getIntervalInSeconds($initialDate, $finalDate)
    {
        if (($initialDate instanceof DateTime) === false) {
            $initialDate = self::getDateFromString($initialDate);
        }

        if (($finalDate instanceof DateTime) === false) {
            $finalDate = self::getDateFromString($finalDate);
        }

        /**
         * @var $initialDate DateTime
         * @var $finalDate DateTime
         * @var $interval \DateInterval
         */
        $interval = $initialDate->diff($finalDate);

        $intervalInDays     = (int)$interval->format('%R%a');
        $intervalInHours    = (int)$interval->format('%R%h');
        $intervalInMinutes  = (int)$interval->format('%R%i');
        $intervalInSeconds  = (int)$interval->format('%R%s');

        $intervalInHours    = $intervalInHours    + $intervalInDays    * 24;
        $intervalInMinutes  = $intervalInMinutes  + $intervalInHours   * 60;

        return $intervalInSeconds + $intervalInMinutes * 60;
    }

    /**
     * Retorna a mesma data passada por parametro truncada (sem data, hora e minuto).
     *
     * @static
     * @param DateTime $date
     * @return DateTime
     */
    public static function truncateData(DateTime $date)
    {
        $strDate = $date->format(self::DEFAULT_FORMAT);
        $strDate .= " 00:00:00";
        return DateTime::createFromFormat(self::DEFAULT_DATETIME_FORMAT, $strDate);
    }

    /**
     * Receive as parameter a date in string format. returns this string in the format passed as parameter.
     *
     * @static
     *
     * @param string $date         Date string.
     * @param string $inputFormat  Input format.
     * @param string $outputFormat Output format.
     *
     * @return string
     */
    public static function truncateString($date,
                                          $inputFormat = self::DEFAULT_DATETIME_FORMAT,
                                          $outputFormat = self::DEFAULT_FORMAT)
    {
        $date = self::getDateFromString($date, $inputFormat);
        return $date->format($outputFormat);
    }

    /**
     * Verify if a string is equals a certain format.
     *
     * @static
     * @param string $date   The date string.
     * @param string $format The format that its expected the date string is.
     *
     * @return bool
     */
    public static function isDateOnFormat($date, $format)
    {
        return (bool) \DateTime::createFromFormat($format, $date);
    }


    /**
     * adjust a dateTime by the difference between two dates. If the initial datetime is lower than the final datetime,
     * the difference is added to the date to be adjusted. Otherwise, the difference is subtracted from it.
     *
     * @static
     * @param string $dateToAdjust   The datetime to adjust.
     * @param string $baseDate1      The first datetime to be compared
     * @param string $baseDate2      The second datetime to be compared
     *
     * @return bool
     */
    public static function adjustDateByServerDiference($dateToAdjust, $initialDate,$finalDate){                   

        if (($initialDate instanceof DateTime) === false) {
            $initialDate = self::getDateFromString($initialDate,self::DEFAULT_DATETIME_FORMAT);
        }        
        if (($finalDate instanceof DateTime) === false) {             
            $finalDate = self::getDateFromString($finalDate,self::DEFAULT_DATETIME_FORMAT);
        }
        if (($dateToAdjust instanceof DateTime) === false) {
            $dateToAdjust = self::getDateFromString($dateToAdjust,self::DEFAULT_DATETIME_FORMAT);            
        }
        


        $diference = $initialDate->diff($finalDate); 
        $dateToAdjust->add($diference); 
        return  $dateToAdjust;
    }

}