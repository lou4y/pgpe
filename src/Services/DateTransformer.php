<?php
namespace App\Services;

use Symfony\Component\Intl\Intl;

class DateTransformer
{
    public function transformToDate($dateString): string
    {
        // Convert date string to DateTime object
        $dateTime = \DateTime::createFromFormat('d:m:Y', $dateString);

        if (!$dateTime) {
            throw new \InvalidArgumentException('Invalid date format. Use jj:mm:yyyy format.');
        }

        // Get the day of the week from the DateTime object
        $dayOfWeekNumber = (int)$dateTime->format('N');
        $daysOfWeek = [
            1 => 'Lundi',
            2 => 'Mardi',
            3 => 'Mercredi',
            4 => 'Jeudi',
            5 => 'Vendredi',
            6 => 'Samedi',
            7 => 'Dimanche',
        ];

        return $daysOfWeek[$dayOfWeekNumber];
    }
}
