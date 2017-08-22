<?php

/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 9/6/2015
 * Time: 9:14 AM
 */
class TDateRepeatRule
{
    private static $dow = array(
        'SU' => 'Sunday',
        'MO' => 'Monday',
        'TU' =>  'Tuesday',
        'WE' =>  'Wednesday',
        'TH' =>  'Thursday',
        'FR' =>  'Friday',
        'SA' =>  'Saturday',
    );


    public function format($rrule, $format = 'D M d Y')
    {
        // Empty or invalid value.
        if (empty($rrule) || !strstr($rrule, 'RRULE')) {
            return '';
        }
        // Make sure there will be an empty description for any unused parts.
        $description = array(
            '!interval' => '',
            '!byday' => '',
            '!bymonth' => '',
            '!count' => '',
            '!until' => '',
            '!except' => '',
            '!additional' => '',
            '!week_starts_on' => '',
        );
        $parts = date_repeat_split_rrule($rrule);
        $additions = $parts[2];
        $exceptions = $parts[1];
        $rrule = $parts[0];
        $interval = INTERVAL_options();
        switch ($rrule['FREQ']) {
            case 'WEEKLY':
                $description['!interval'] = format_plural($rrule['INTERVAL'], 'every week', 'every @count weeks') . ' ';
                break;
            case 'MONTHLY':
                $description['!interval'] = format_plural($rrule['INTERVAL'], 'every month', 'every @count months') . ' ';
                break;
            case 'YEARLY':
                $description['!interval'] = format_plural($rrule['INTERVAL'], 'every year', 'every @count years') . ' ';
                break;
            default:
                $description['!interval'] = format_plural($rrule['INTERVAL'], 'every day', 'every @count days') . ' ';
                break;
        }

        if (!empty($rrule['BYDAY'])) {
            $days = date_repeat_dow_day_options();
            $counts = date_repeat_dow_count_options();
            $results = array();
            foreach ($rrule['BYDAY'] as $byday) {
                $day = drupal_substr($byday, -2);
                $count = intval(str_replace(' ' . $day, '', $byday));
                if ($count = intval(str_replace(' ' . $day, '', $byday))) {
                    $results[] = trim(t('!repeats_every_interval on the !date_order !day_of_week', array('!repeats_every_interval ' => '', '!date_order' => strtolower($counts[drupal_substr($byday, 0, 2)]), '!day_of_week' => $days[$day])));
                } else {
                    $results[] = trim(t('!repeats_every_interval every !day_of_week', array('!repeats_every_interval ' => '', '!day_of_week' => $days[$day])));
                }
            }
            $description['!byday'] = implode(' ' . t('and') . ' ', $results);
        }
        if (!empty($rrule['BYMONTH'])) {
            if (sizeof($rrule['BYMONTH']) < 12) {
                $results = array();
                $months = date_month_names();
                foreach ($rrule['BYMONTH'] as $month) {
                    $results[] = $months[$month];
                }
                if (!empty($rrule['BYMONTHDAY'])) {
                    $description['!bymonth'] = trim(t('!repeats_every_interval on the !month_days of !month_names', array('!repeats_every_interval ' => '', '!month_days' => implode(', ', $rrule['BYMONTHDAY']), '!month_names' => implode(', ', $results))));
                } else {
                    $description['!bymonth'] = trim(t('!repeats_every_interval on !month_names', array('!repeats_every_interval ' => '', '!month_names' => implode(', ', $results))));
                }
            }
        }
        if ($rrule['INTERVAL'] < 1) {
            $rrule['INTERVAL'] = 1;
        }
        if (!empty($rrule['COUNT'])) {
            $description['!count'] = trim(t('!repeats_every_interval !count times', array('!repeats_every_interval ' => '', '!count' => $rrule['COUNT'])));
        }
        if (!empty($rrule['UNTIL'])) {
            $until = date_ical_date($rrule['UNTIL'], 'UTC');
            date_timezone_set($until, date_default_timezone());
            $description['!until'] = trim(t('!repeats_every_interval until !until_date', array('!repeats_every_interval ' => '', '!until_date' => date_format_date($until, 'custom', $format))));
        }
        if ($exceptions) {
            $values = array();
            foreach ($exceptions as $exception) {
                $values[] = date_format_date(date_ical_date($exception), 'custom', $format);
            }
            $description['!except'] = trim(t('!repeats_every_interval except !except_dates', array('!repeats_every_interval ' => '', '!except_dates' => implode(', ', $values))));
        }
        if ($additions) {
            $values = array();
            foreach ($additions as $addition) {
                $values[] = date_format_date(date_ical_date($addition), 'custom', $format);
            }
            $description['!additional'] = trim(t('Also includes !additional_dates.', array('!additional_dates' => implode(', ', $values))));
        }
        if (!empty($rrule['WKST'])) {
            $day_names = date_repeat_dow_day_options();
            $description['!week_starts_on'] = trim(t('!repeats_every_interval where the week start on !day_of_week', array('!repeats_every_interval ' => '', '!day_of_week' => $day_names[trim($rrule['WKST'])])));
        }
        return t('Repeats !interval !bymonth !byday !count !until !except. !additional', $description);
    }
}

