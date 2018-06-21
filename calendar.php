<?php
require_once 'jdatetime.class.php';

/**
 * @author  Xu Ding
 * @email   thedilab@gmail.com
 * @website http://www.StarTutorial.com
 * */
class Calendar {

    private function dateme($i) {
        $conn = mysqli_connect("localhost", "root", "", "progcv");
        $query = mysqli_query($conn, "SELECT * FROM calendar WHERE `date`='" . $i . "'");
        if ($query) {
            if (mysqli_num_rows($query) > 0) {
                $rest = true;
            } else {
                $rest = false;
            }
        }
        return $rest;
    }

    /**
     * Constructor
     */
    public function __construct() {
        $this->naviHref = htmlentities($_SERVER['PHP_SELF']);
    }

    /*     * ******************* PROPERTY ******************* */

    //private $dayLabels = array("Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun");
    private $dayLabels = array("شنبه", "یک شنبه", "دوشنبه", "سه شنبه", "چهارشنبه", "پنج شنبه", "جمعه");
    private $currentYear = 0;
    private $currentMonth = 0;
    private $currentDay = 0;
    private $currentDate = null;
    private $daysInMonth = 0;
    private $naviHref = null;

    /*     * ******************* PUBLIC ********************* */

    /**
     * print out the calendar
     */
    public function show() {
        $date = new jDateTime();
        $year = null;

        $month = null;

        if (null == $year && isset($_GET['year'])) {

            $year = $_GET['year'];
        } else if (null == $year) {

            //$year = date("Y", time());
            $year = $date->date("Y", time(),false, 'Asia/Tehran');
        }

        if (null == $month && isset($_GET['month'])) {

            $month = $_GET['month'];
        } else if (null == $month) {

            //$month = date("m", time());
            $month = $date->date("m", time(),false, 'Asia/Tehran');
        }

        $this->currentYear = $year;

        $this->currentMonth = $month;

        $this->daysInMonth = $this->_daysInMonth($month, $year);

        $content = '<div id="calendar">' .
                '<div class="box">' .
                $this->_createNavi() .
                '</div>' .
                '<div class="box-content">' .
                '<ul class="label">' . $this->_createLabels() . '</ul>';
        $content .= '<div class="clear"></div>';
        $content .= '<ul class="dates">';

        $weeksInMonth = $this->_weeksInMonth($month, $year);
        // Create weeks in a month
        for ($i = 0; $i < $weeksInMonth; $i++) {

            //Create days in a week
            for ($j = 1; $j <= 7; $j++) {
                $content .= $this->_showDay($i * 7 + $j);
            }
        }

        $content .= '</ul>';

        $content .= '<div class="clear"></div>';

        $content .= '</div>';

        $content .= '</div>';
        return $content;
    }

    /*     * ******************* PRIVATE ********************* */

    /**
     * create the li element for ul
     */
    private function _showDay($cellNumber) {
        $date = new jDateTime();
        if ($this->currentDay == 0) {

            //$firstDayOfTheWeek = date('N', strtotime($this->currentYear . '-' . $this->currentMonth . '-01'));
            $time = $date->mktime(0,0,0,$this->currentMonth,1,$this->currentYear);
            $firstDayOfTheWeek = $date->date("N", $time,false, 'Asia/Tehran');
            if (intval($cellNumber) == intval($firstDayOfTheWeek)) {

                $this->currentDay = 1;
            }
        }
        if (($this->currentDay != 0) && ($this->currentDay <= $this->daysInMonth)) {

            //$this->currentDate = date('Y-m-d', strtotime($this->currentYear . '-' . $this->currentMonth . '-' . ($this->currentDay)));
            $time = $date->mktime(0,0,0,$this->currentMonth,$this->currentDay,$this->currentYear);
            $this->currentDate = $date->date('Y-m-d', $time,false, 'Asia/Tehran');
            $cellContent = $this->currentDay;

            $this->currentDay++;
        } else {

            $this->currentDate = null;

            $cellContent = null;
        }
        $d = $this->dateme($this->currentDate);
        if ($d) {
            $li = '<li id="li-' . $this->currentDate . '" class="haveevent"' . ($cellNumber % 7 == 1 ? ' start ' : ($cellNumber % 7 == 0 ? ' end ' : ' ')) .
                    ($cellContent == null ? 'mask' : '') . '"><a href="showevent.php?date=' . $this->currentDate . '">' . $cellContent . '</a></li>';
        } else {
            $li = '<li id="li-' . $this->currentDate . '" class="' . ($cellNumber % 7 == 1 ? ' start ' : ($cellNumber % 7 == 0 ? ' end ' : ' ')) .
                    ($cellContent == null ? 'mask' : '') . '">' . $cellContent . '</li>';
        }
        return $li;
    }

    /**
     * create navigation
     */
    private function _createNavi() {
        $date = new jDateTime();
        $nextMonth = $this->currentMonth == 12 ? 1 : intval($this->currentMonth) + 1;

        $nextYear = $this->currentMonth == 12 ? intval($this->currentYear) + 1 : $this->currentYear;

        $preMonth = $this->currentMonth == 1 ? 12 : intval($this->currentMonth) - 1;

        $preYear = $this->currentMonth == 1 ? intval($this->currentYear) - 1 : $this->currentYear;
        $time = $date->mktime(0,0,0,$this->currentMonth,1,$this->currentYear);
        return
                '<div class="header">' .
                '<a class="prev" href="' . $this->naviHref . '?month=' . sprintf('%02d', $preMonth) . '&year=' . $preYear . '">ماه قبل</a>' .
                //'<span class="title">' . date('Y M', strtotime($this->currentYear . '-' . $this->currentMonth . '-1')) . '</span>' .
                '<span class="title">' . $date->date("Y F", $time,false, 'Asia/Tehran') . '</span>' .
                '<a class="next" href="' . $this->naviHref . '?month=' . sprintf("%02d", $nextMonth) . '&year=' . $nextYear . '">ماه بعد</a>' .
                '</div>';
    }

    /**
     * create calendar week labels
     */
    private function _createLabels() {

        $content = '';

        foreach ($this->dayLabels as $index => $label) {

            $content .= '<li class="' . ($label == 6 ? 'end title' : 'start title') . ' title">' . $label . '</li>';
        }

        return $content;
    }

    /**
     * calculate number of weeks in a particular month
     */
    private function _weeksInMonth($month = null, $year = null) {
        $date = new jDateTime();
        if (null == ($year)) {
            //$year = date("Y", time());
            $year = $date->date("Y", time(),false, 'Asia/Tehran');
        }

        if (null == ($month)) {
            //$month = date("m", time());
            $month = $date->date("m", time(),false, 'Asia/Tehran');
        }

        // find number of days in this month
        $daysInMonths = $this->_daysInMonth($month, $year);

        $numOfweeks = ($daysInMonths % 7 == 0 ? 0 : 1) + intval($daysInMonths / 7);

        //$monthEndingDay = date('N', strtotime($year . '-' . $month . '-' . $daysInMonths));
        $monthEndingDay = $date->date("N", strtotime($year . '-' . $month . '-' . $daysInMonths),false, 'Asia/Tehran');

        //$monthStartDay = date('N', strtotime($year . '-' . $month . '-01'));
        $monthStartDay = $date->date("N", strtotime($year . '-' . $month . '-01'),false, 'Asia/Tehran');

        if ($monthEndingDay < $monthStartDay) {

            $numOfweeks++;
        }

        return $numOfweeks;
    }

    /**
     * calculate number of days in a particular month
     */
    private function _daysInMonth($month = null, $year = null) {
        $date = new jDateTime();
        if (null == ($year)){
        //$year = date("Y", time());
            $year = $date->date("Y", time(),false, 'Asia/Tehran');
        }
        if (null == ($month)){
        //$month = date("m", time());
            $month = $date->date("m", time(),false, 'Asia/Tehran');
        }
        //return date('t', strtotime($year . '-' . $month . '-01'));
        $time = $date->mktime(0,0,0,$month,1,$year);
        return $date->date("t", $time,false, 'Asia/Tehran');
    }

}