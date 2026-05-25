<?php

namespace Tests\Unit;

use App\Services\QuotationCalculator;
use Carbon\CarbonImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class QuotationCalculatorTest extends TestCase
{
    public function test_it_calculates_the_documented_example_with_inclusive_days(): void
    {
        $calculation = (new QuotationCalculator)->calculate(
            [28, 35],
            CarbonImmutable::parse('2020-10-01'),
            CarbonImmutable::parse('2020-10-30'),
        );

        $this->assertSame('117.00', $calculation['total']);
        $this->assertSame(30, $calculation['tripLength']);
    }

    public function test_it_rejects_an_age_outside_the_supported_load_table(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new QuotationCalculator)->calculate(
            [17],
            CarbonImmutable::parse('2026-06-01'),
            CarbonImmutable::parse('2026-06-02'),
        );
    }

    public function test_it_rejects_an_end_date_before_the_start_date(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new QuotationCalculator)->calculate(
            [28],
            CarbonImmutable::parse('2026-06-02'),
            CarbonImmutable::parse('2026-06-01'),
        );
    }
}
