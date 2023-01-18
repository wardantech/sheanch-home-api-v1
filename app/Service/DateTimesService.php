<?php

namespace App\Service;

class DateTimesService
{
    public static function monthBetweenDate($start_date, $end_date): int
    {
        $deedStartMonth = date("Y-m-d", strtotime($start_date)) ;
        $currentMonth = date('Y-m-d', strtotime($end_date));

        $date1 = new \DateTime($deedStartMonth);
        $date2 = new \DateTime($currentMonth);
        $interval = $date1->diff($date2);
        $months = $interval->format("%m");

        return $months;
    }
}
