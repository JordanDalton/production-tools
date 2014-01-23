<?php
/**
 * Functions for working with dates
 *
 * @category WrsGroup
 * @package Date
 * @author Eugene Morgan <eugenemorgan@wrsgroup.com>
 */
class WrsGroup_Date extends Zend_Date
{
    /**
     * Converts an AS400 date field to a date object
     *
     * @param string $dateString A 5 or 6 digit date string
     * @return WrsGroup_Date A date object
     */
    public static function getAs400Date($dateString)
    {
        $len = mb_strlen($dateString);
        if ($len < 5 || $len > 6) {
            throw new Exception('Unexpected date string; date should be 5 or ' .
                '6 digits long');
        }
        $date = new WrsGroup_Date();
        if ($len == 5) {
            $dateString = '0' . $dateString;
        }
        $date->set($dateString, 'yyMMdd');
        return $date;
    }

    /**
     * Tells whether the given date object refers to a business day
     *
     * @return boolean Whether or not the day is a business day
     */
    public function isBusinessDay()
    {
        $weekday = $this->get(Zend_Date::WEEKDAY_SHORT);
        if ($weekday == 'Sat' || $weekday == 'Sun') {
            return false;
        }

        $holidaysByDate = array(
            'Jan 01',
            'Jul 04',
            'Dec 24',
            'Dec 25',
        );

        $month = $this->toString('MMM');
        $day = $this->toString('dd');
        if (in_array($month . ' ' . $day, $holidaysByDate)) {
            return false;
        }

        if ($weekday == 'Mon') {
            // Is it the last Monday in May?
            if ($month == 'May' && $day > 24) {
                return false;
            }

            // Is it the first Monday in Sep?
            if ($month == 'Sep' && $day < 8) {
                return false;
            }

            // Is it one greater than a dated holiday?
            if ($day != '01') {
                $newDate = new WrsGroup_Date();
                $newDate->set($this->toString('yyyyMMdd'), 'yyyyMMdd');
                $newDate->sub('1', Zend_Date::DAY);
                $previousDay = $newDate->toString('dd');
                if (in_array($month . ' ' . $previousDay, $holidaysByDate)) {
                    return false;
                }
            }
        }

        if ($weekday == 'Tue') {
            // Is it Tue, Dec 26?
            if ($month == 'Dec' && $day == 26) {
                return false;
            }
        }

        if ($weekday == 'Thu') {
            // Is it the fourth Thurs. in November?
            if ($month == 'Nov' && $day > 21 && $day < 29) {
                return false;
            }

            // Is it Thu, Dec 23?
            if ($month == 'Dec' && $day == 23) {
                return false;
            }
        }

        if ($weekday == 'Fri') {
            // Is it the day after Thanksgiving?
            if ($month == 'Nov' && $day > 22 && $day < 30) {
                return false;
            }

            // Is it the day before a dated holiday?
            $newDate = new WrsGroup_Date();
            $newDate->set($this->toString('yyyyMMdd'), 'yyyyMMdd');
            $newDate->add('1', Zend_Date::DAY);
            $nextDatePart = $newDate->toString('MMM dd');
            if (in_array($nextDatePart, $holidaysByDate)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Sets the date as the last business day of the month currently set
     * in the date object
     *
     * @return WrsGroup_Date Fluent interface
     */
    public function setLastBusinessDayOfMonth()
    {
        $days = $this->get(Zend_Date::MONTH_DAYS);
        $this->set($days, Zend_Date::DAY);
        while (!$this->isBusinessDay()) {
            $this->sub(1, Zend_Date::DAY);
        }
        return $this;
    }

    /**
     * Adds specified number of business days to the current date object
     *
     * @param integer Number of days to add; default is 1
     * @return WrsGroup_Date Fluent interface
     */
    public function addBusinessDays($days = 1)
    {
        for ($i = 0; $i < $days; $i++) {
            do {
                $this->add(1, Zend_Date::DAY);
            } while (!$this->isBusinessDay());
        }
        return $this;
    }

    /**
     * Subtracts specified number of business days from the current date object
     *
     * @param integer Number of days to subtract; default is 1
     * @return WrsGroup_Date Fluent interface
     */
    public function subBusinessDays($days = 1)
    {
        for ($i = 0; $i < $days; $i++) {
            do {
                $this->sub(1, Zend_Date::DAY);
            } while (!$this->isBusinessDay());
        }
        return $this;
    }

    /**
     * Calculates the number of business days between the date object and
     * a given end date. Includes the start and end date in the calculation,
     * so for example if you had today as the start date and tomorrow
     * as the end date, and they were both business days, the result would be 2.
     *
     * @param Zend_Date $endDate A date object
     * @return integer The number of business days
     */
    public function calcBusinessDays($endDate)
    {
        if ($this->toString('yyyyMMdd') >= $endDate->toString('yyyyMMdd')) {
            $msg = 'End date must be at least a day after start date.';
            throw new Exception($msg);
        }

        $days = 0;
        $date = new WrsGroup_Date();
        $date->set($this->toString('yyyyMMdd'), 'yyyyMMdd');
        if ($this->isBusinessDay()) {
            $days++;
        }
        $date->addBusinessDays(1);
        while ($date->toString('yyyyMMdd') <= $endDate->toString('yyyyMMdd')) {
            $days++;
            $date->addBusinessDays(1);
        }
        return $days;
    }
}
