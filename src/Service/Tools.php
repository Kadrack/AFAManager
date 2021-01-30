<?php

namespace App\Service;

/**
 * Class Tools
 * @package App\Service
 */
class Tools
{
    /**
     * @param string $time
     * @param string $format
     * @return string
     */
    public function convertToHoursMins($time, $format = '%02d:%02d')
    {
        if ($time < 1) {
            return '';
        }
        $hours = floor($time / 60);
        $minutes = ($time % 60);
        return sprintf($format, $hours, $minutes);
    }
}