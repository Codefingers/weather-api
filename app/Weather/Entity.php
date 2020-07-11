<?php
declare(strict_types = 1);

namespace App\Weather;

/**
 * Entity representing weather
 */
class Entity implements \JsonSerializable
{
    /** @var float Current temperature in a Kelvin unit representation */
    private float $temperature;

    /** @var float Value representing absolute zero in the Kelvin scale, used to convert temperatures to different scales */
    private float $absoluteZero = 273.15;

    /**
     * Constructor.
     *
     * @param float $temperature Current temperature
     */
    public function __construct(float $temperature)
    {
        $this->temperature = $temperature;
    }

    /**
     * Gets the current temperature as a Celsius unit
     *
     * @return float
     */
    public function getTemperatureInCelsius(): float
    {
        return $this->temperature;
    }

    /**
     * Converts the temperature to Fahrenheit from Celsius
     *
     * @return float
     */
    public function getTemperatureInFahrenheit(): float
    {
        return ($this->temperature * 1.8) + 32;
    }

    /**
     * Converts the temperature to Kelvin from Celsius
     *
     * @return float
     */
    public function getTemperatureInKelvin(): float
    {
        return $this->temperature + $this->absoluteZero;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return [
            'celsius'    => number_format($this->getTemperatureInCelsius(), 2),
            'fahrenheit' => number_format($this->getTemperatureInFahrenheit(), 2),
            'kelvin'     => number_format($this->getTemperatureInKelvin(), 2),
        ];
    }
}
