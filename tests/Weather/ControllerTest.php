<?php
declare(strict_types = 1);

namespace App\Weather;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use TestCase;

/**
 * Exercises Weather Controller functionality
 */
class ControllerTest extends TestCase
{
    /**
     * Tests that the controller will return the expected response given different inputs
     *
     * @param array  $guzzleBody       Mock body of guzzle response
     * @param string $city             City to get weather for
     * @param array  $expectedResponse Expected response from API
     *
     * @return void
     * @dataProvider dataGet
     */
    public function testGet(array $guzzleBody, string $city, array $expectedResponse): void
    {
        $fakeApiKey = env('OPEN_WEATHER_API_KEY');

        $params = [
            'q'     => $city,
            'appid' => $fakeApiKey,
            'units' => 'metric',
        ];
        $response = new Response(200, [], json_encode($guzzleBody));

        $mockClient = $this->createMock(Client::class);
        $mockClient->expects($this->once())
            ->method('get')
            ->with(WeatherController::OPEN_WEATHER_MAP_BASE_URL, ['query' => $params])
            ->willReturn($response);

        // Mock dependency of controller by replacing instance in container with a mock
        $this->app->instance(Client::class, $mockClient);
        $this->get("api/weather/{$city}");

        $this->assertSame(
            $expectedResponse,
            json_decode($this->response->getContent(), true)
        );

        $this->assertSame(200, $this->response->getStatusCode());
    }

    /**
     * Dataprovider for testGet
     *
     * @return array
     */
    public function dataGet(): array
    {
        return [
            'Basic success example'                  => [
                'guzzle body'       => [
                    'main' => [
                        'temp' => '19.27',
                    ],
                ],
                'city'              => 'London',
                'expected response' => [
                    'celsius'    => '19.27',
                    'fahrenheit' => '66.69',
                    'kelvin'     => '292.42',
                ],
            ],
            'Basic success example for another city' => [
                'guzzle body'       => [
                    'main' => [
                        'temp' => '36.95',
                    ],
                ],
                'city'              => 'Vilnius',
                'expected response' => [
                    'celsius'    => '36.95',
                    'fahrenheit' => '98.51',
                    'kelvin'     => '310.10',
                ],
            ],
        ];
    }

    /**
     * Tests that an exception returned from Guzzle is handled and the APIs response is returned
     *
     * @return void
     */
    public function testGetGuzzleExceptions(): void
    {
        $fakeApiKey = env('OPEN_WEATHER_API_KEY');
        $statusCode = 404;
        $guzzleBody = [
            'cod'     => 404,
            'message' => 'Weather for foo bar could not be found',
        ];
        $errorMessage = 'Weather for foo bar could not be found';

        $response = new Response($statusCode, [], json_encode($guzzleBody));
        $clientException = new ClientException($errorMessage, $this->createMock(Request::class), $response);

        $params = [
            'q'     => 'foo bar',
            'appid' => $fakeApiKey,
            'units' => 'metric',
        ];

        $mockClient = $this->createMock(Client::class);
        $mockClient->expects($this->once())
            ->method('get')
            ->with(WeatherController::OPEN_WEATHER_MAP_BASE_URL, ['query' => $params])
            ->willThrowException($clientException);

        // Mock dependency of controller by replacing instance in container with a mock
        $this->app->instance(Client::class, $mockClient);
        $this->get("api/weather/foo bar");

        $this->assertSame(
            [
                'cod'     => $statusCode,
                'message' => $errorMessage,
            ],
            json_decode($this->response->getContent(), true)
        );

        $this->assertSame($statusCode, $this->response->getStatusCode());
    }

    /**
     * Tests that when no API Key exists, an exception is handled and returned as a 500 response
     *
     * @return void
     */
    public function testGetNoApiKey(): void
    {
        // mock the env service to return no key
        $mockEnvService = $this->createMock(EnvironmentService::class);
        $mockEnvService->method('get')->with('OPEN_WEATHER_API_KEY')->willReturn('');

        $mockClient = $this->createMock(Client::class);
        $mockClient->expects($this->never())->method('get');

        // Mock dependency of controller by replacing instance in container with a mock
        $this->app->instance(Client::class, $mockClient);
        $this->app->instance(EnvironmentService::class, $mockEnvService);

        $this->get("api/weather/London");
        $this->assertSame('API key has not been provided', $this->response->exception->getMessage());
        $this->assertSame(500, $this->response->getStatusCode());
    }
}
