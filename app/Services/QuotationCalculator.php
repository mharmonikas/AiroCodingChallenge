<?php

namespace App\Services;

use Carbon\CarbonImmutable;
use InvalidArgumentException;

class QuotationCalculator
{
    private const float DAILY_RATE = 3.0;

    /**
     * @param  list<int>  $ages
     * @return array{total: string, tripLength: int}
     */
    public function calculate(array $ages, CarbonImmutable $startDate, CarbonImmutable $endDate): array
    {
        if ($endDate->isBefore($startDate)) {
            throw new InvalidArgumentException('End date must be same as or after the start date.');
        }

        $tripLength = (int) $startDate->diffInDays($endDate) + 1;
        $dailyAgeLoad = array_sum(array_map(
            fn (int $age): float => $this->ageLoad($age),
            $ages,
        ));

        return [
            'total' => number_format(self::DAILY_RATE * $dailyAgeLoad * $tripLength, 2, '.', ''),
            'tripLength' => $tripLength,
        ];
    }

    private function ageLoad(int $age): float
    {
        return match (true) {
            $age >= 18 && $age <= 30 => 0.6,
            $age >= 31 && $age <= 40 => 0.7,
            $age >= 41 && $age <= 50 => 0.8,
            $age >= 51 && $age <= 60 => 0.9,
            $age >= 61 && $age <= 70 => 1.0,
            default => throw new InvalidArgumentException('Age must be between 18 and 70.'),
        };
    }
}
