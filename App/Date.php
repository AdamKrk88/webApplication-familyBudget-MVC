<?php

namespace App;

use \DateTime;

/**
 * Work with date
 *
 * PHP version 7.2.0
 */

class Date 
{
    public static function isCurrentMonthDate($dateFromDatabase) {
        $dateCurrent = date('Y-m-d');
        $date = new DateTime($dateCurrent);
        $lastDayOfCurrentMonth = $date->format('Y-m-t'); 
        $firstDayOfCurrentMonth = date('Y-m-01');
        
        if ($firstDayOfCurrentMonth <= $dateFromDatabase && $lastDayOfCurrentMonth >= $dateFromDatabase) {
            return true;
        }
        else {
            return false;
        }
    }

    public static function isPreviousMonthDate($dateFromDatabase) {
        $lastDayOfPreviousMonth = date('Y-m-d', strtotime("last day of -1 month"));
        $firstDayOfPreviousMonth = date('Y-m-d', strtotime("first day of -1 month"));
            
        if ($firstDayOfPreviousMonth <= $dateFromDatabase && $lastDayOfPreviousMonth >= $dateFromDatabase) {
            return true;
        }
        else {
            return false;
        }
        
    }

    public static function isCurrentYearDate($dateFromDatabase) 
    {
        $firstDayOfCurrentYear = date('Y-m-d', strtotime('first day of january this year'));
        $lastDayOfCurrentYear = date('Y-m-d', strtotime('last day of december this year'));
        
        if ($firstDayOfCurrentYear <= $dateFromDatabase && $lastDayOfCurrentYear >= $dateFromDatabase) {
            return true;
        }
        else {
            return false;
        }

    }

    public static function isDateFromModal($dateFromDatabase, $dateFromModalFrom, $dateFromModalTo) 
    {   
        if ($dateFromModalFrom <= $dateFromDatabase && $dateFromModalTo >= $dateFromDatabase) {
            return true;
        }
        else {
            return false;
        }

    }
}