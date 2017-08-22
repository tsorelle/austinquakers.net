<?php
/*****************************************************************

Some usefull datetime functions. Call as static methods, e.g.

TDateTime_longDateUS($mydate)

                               12/5/2006 3:13PM
*****************************************************************/
define('US_LONG_DATE_FORMAT','l F j, Y');
define('US_SHORT_DATE_FORMAT','n/j/Y');
define('MYSQL_DATE_FORMAT','Y-m-d');

// Style for static methods in php 4 not supported in 5
// 5 requires static keyword 4 raises error is static is used.
//class TDateTime
// {
    function TDateTime_ShortUStoMySql($dateString)
    {
        list ($month, $day, $year) = split ('[/.-]', $dateString);

        if (strlen($month) == 1)
            $month = '0'.$month;
        if (strlen($day) == 1)
            $day = '0'.$day;
        if (strlen($year) == 2)
            $year = '20'.$year;

        return $year.'-'.$month.'-'.$day;
    }

    function TDateTime_todayMySql()
    {
        return date(MYSQL_DATE_FORMAT);
    }  //  MySqlToday

    function TDateTime_todayLongUS()
    {
        return date(US_LONG_DATE_FORMAT);

    }  //  todayLongUS

    function TDateTime_todayShortUS()
    {

        return date(US_SHORT_DATE_FORMAT);
    }  //  todayShortUS

    function TDateTime_format($format, $dateString) {
        if (empty($dateString))
          return '';
        return date($format,strToTime($dateString));
    }

    function TDateTime_longDateUS($dateString)
    {
        return TDateTime_format(US_LONG_DATE_FORMAT,$dateString);
    }  //  fullDate

    function TDateTime_shortDateUS($dateString)
    {
        return TDateTime_format(US_SHORT_DATE_FORMAT,$dateString);
    }  //  ShortUS

    function TDateTime_formatTime($value) {
        if (empty($value))
            return '';
        if ($value == '1200' || $value == '12:00')
            return 'Noon';

        $parts = explode(':',$value);
        if (count(parts) == 1) {
            $hr = intval(substr($value,0,2));
            $mn = substr($value,2);
        }
        else {
            $hr = intval($parts[0]);
            $mn = $parts[1];
        }
        if ($hr == 12) {
            $ampm = 'pm';
        }
        else if ($hr > 12) {
            $hr -= 12;
            $ampm = 'pm';
        }
        else {
            $ampm = 'am';
            if ($hr == 0)
              $hr = '12';
        }
        return "$hr:$mn $ampm";
    }

    function TDateTime_MySqlFancyDate($fieldName)
    {
        // composes a MySql function that formats a date like
        // Friday March 1st, 2007
        return "DATE_FORMAT($fieldName,'%W %M %D, %Y')";
    }  //  MySqlFancyDate

// }   // finish class TDateTime


