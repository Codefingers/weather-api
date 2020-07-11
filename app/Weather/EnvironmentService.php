<?php
declare(strict_types=1);

namespace App\Weather;

/**
 * Service for interacting with environment variables
 * The main purpose of this value is to allow environment variables to be mocked for the purpose of testing
 *
 * @package Weather
 */
class EnvironmentService
{
    /**
     * Returns the environment value for the given key
     *
     * @param string $key Key to get from the environment values
     *
     * @return mixed
     */
    public function get(string $key)
    {
        return env($key);
    }
}
