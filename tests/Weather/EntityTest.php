<?php
declare(strict_types = 1);

namespace App\Weather;

/**
 * Exercises the Entity value object
 */
class EntityTest extends \TestCase
{
    /**
     * Tests that the expected value is returned when we ask for the temperature as a Celsius unit
     *
     * @return void
     */
     public function testGetTemperatureInCelsius(): void
     {
         $entity = new Entity(18.10);
         $this->assertSame(18.10, $entity->getTemperatureInCelsius());
     }

    /**
     * Tests that the expected value is returned when we ask for the temperature as a Kelvin unit
     *
     * @return void
     */
     public function testGetTemperatureInKelvin(): void
     {
         $entity = new Entity(18.10);
         $this->assertSame(291.25, $entity->getTemperatureInKelvin());
     }

    /**
     * Tests that the expected value is returned when we ask for the temperature as a Fahrenheit unit
     *
     * @return void
     */
     public function testGetTemperatureInFahrenheit(): void
     {
         $entity = new Entity(18.10);
         $this->assertSame(64.58, $entity->getTemperatureInFahrenheit());
     }
}
