<?php

namespace App\Tests\Entity;

use App\Entity\Measurement;
use PHPUnit\Framework\TestCase;

class MeasurementTest extends TestCase
{
    public function dataGetFahrenheit(): array
    {
        return [
            ['0', 32],
            ['-100', -148],
            ['100', 212],
            ['27', 80.6],
            ['-6', 21.2],
            ['94', 201.2],
            ['-21', -5.8],
            ['37', 98.6],
            ['41.5', 106.7],
            ['88.8', 191.84],
        ];
    }
    /**
     *@dataProvider dataGetFahrenheit
     */
    public function testGetFahrenheit($celsius, $expectedFahrenheit): void
    {
        $measurements = new Measurement();

        $measurements->setCelsius($celsius);
        $this->assertEquals(
            $expectedFahrenheit,
            $measurements->getFahrenheit(),
            "Expected $expectedFahrenheit F for $celsius C, got {$measurements->getFahrenheit()}"
        );
    }
}
