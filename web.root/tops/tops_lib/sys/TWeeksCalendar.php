<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 5/17/2015
 * Time: 6:05 AM
 */

class TWeeksCalendar {

    /**
     * @var array()
     */
    private $dates;
    private $weekCount;
    private $dow;
    private $month;
    private $year;

    public function __construct($month=0,$year=0,$dow='Sunday') {
        $this->setMonth($month,$year,$dow);
    }

    public function setMonth($month=0,$year=0,$dow='Sunday') {
        $this->dates = array();
        $this->dow = $dow;
        // adjust month and year
        if ($year == 0)
            $year = date('Y'); //current Year;
        else if ($year < 1000)
            $year += 2000;

        if ($month == 0) {
            $month = date('m'); //current Month;
        }
        else {
            if ($month > 12) {
                $month = 1;
                $year++;
            }
            else if ($month < 1) {
                $month = 12;
                $year--;
            }
        }

        // calculate date series

        // start with first day of the month
        $day = mktime(0,0,0,$month,1,$year);

        if (date('w',$day)!= 0) {
            $day = strtotime("next $dow", $day);

        }

        $currentMonth = date('m',$day);
        for ($i=0; $i<5; $i++ ) {
            $this->dates[$i+1] = $day;
            $day = strtotime("next $dow",$day);
            if ($currentMonth != date('m',$day)) {
                break;
            }
        }

        $this->weekCount = sizeof($this->dates);
        $this->month = $month;
        $this->year = $year;
    }

    public static function GetCalendar($date = null) {
        if (empty($date)) {
            return new TWeeksCalendar();
        }
        $month = date('m');
        $year = date('Y');
        return new TWeeksCalendar($month,$year);
    }

    private static $ordinals = array('1st','2nd','3rd','4th','5th');
    private function ordinal($week)
    {
        if (!is_numeric($week) || $week < 1 || $week > 5) {
            return '';
        }
        return self::$ordinals[$week-1];
    }


    public function getWeekCount() {
        return $this->weekCount;
    }

    public function getDates() {
        return $this->dates;
    }

    public function getDate($week) {
        if (empty($week) || $week > $this->weekCount) {
            return null;
        }
        return $this->dates[$week];
    }

    public function getDateString($week,$format="Y-m-d") {
        if (empty($week) || $week > $this->weekCount) {
            return '';
        }
        return date($format,$this->dates[$week]);
    }

    public function getSqlDate($week) {
        return date('Y-m-d',$this->dates[$week]);
    }


    public function getDisplayMonth() {
        return $this->getDateString(1,'F');
    }

    public function getDisplayDate($week,$dateFormat='M-d',$format='%s: %s') {
        $datestr = $this->getDateString($week,$dateFormat);
        if (empty($datestr)) {
            return null;
        }
        $ordinal = $this->ordinal($week);
        return sprintf($format,$ordinal,$datestr);
    }

    public function getLongDisplayDate($week) {
        return $this->getDisplayDate($week,'F j, Y' ,'%s '.$this->dow.', %s ');
    }

    public function getStartDate() {
        return $this->getDateString(1);
    }

    public function getEndDate() {
        return $this->getDateString(sizeof($this->dates));
    }

    public static function formatDate($dateValue=null,$format ='Y-m-d')
    {
        if (empty($dateValue)) {
            $result = @date($format);
            if (empty($result)) {
                return false;
            }
            return $result;
        }

        if (!is_numeric($dateValue)) {
            $dateValue = @strtotime($dateValue);
            if ($dateValue === false) {
                return false;
            }
        }
        return @date($format,$dateValue);
    }

    public function getSelectList() {
        $result = array();
        $end = sizeof($this->dates);
        for ($i=1; $i<=$end; $i++) {
            $date = $this->dates[$i];

            $item = new WeekListItem();
            $item->order = $i;
            $item->value = $this->getDateString($i);
            $item->longText = $this->getLongDisplayDate($i);
            $item->shortText= $this->getDateString($i,'M-d');
            array_push($result,$item);

        }
        return $result;
    }

    public function getMonth() {
        return $this->month;
    }

    public function getYear() {
        return $this->year;
    }

    public function getMonthAndYear($format="F, Y") {
        $date = mktime(0,0,0,$this->month,1,$this->year);
        return date($format,$date);
    }



}