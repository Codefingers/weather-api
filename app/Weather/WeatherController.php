<?php
declare(strict_types = 1);

namespace App\Weather;

use App\Http\Controllers\Controller;
use GuzzleHttp;
use Illuminate\Http\JsonResponse;

/**
 * Class WeatherController
 *
 * Controller for handling weather requests
 */
class WeatherController extends Controller
{
    /** @var GuzzleHttp\Client Guzzle client */
    private GuzzleHttp\Client $client;

    /** @var string Base url for the open weather map API */
    const OPEN_WEATHER_MAP_BASE_URL = 'api.openweathermap.org/data/2.5/weather';

    /** @var EnvironmentService Service for handling environment requests */
    private EnvironmentService $service;

    /**
     * Constructor
     *
     * @param GuzzleHttp\Client  $client  Guzzle client
     * @param EnvironmentService $service Service for handling environment requests
     */
    public function __construct(GuzzleHttp\Client $client, EnvironmentService $service)
    {
        $this->client = $client;
        $this->service = $service;
    }

    /**
     * GET action on 'api/weather/{city}'
     *
     * Responsible for returning the current weather for the given city
     *
     * @param string $city City to get weather for. This parameter is validated in route config. It is required to be
     *                     a string. Not providing the city will return a 404 as the route requires a city parameter.
     *
     * @return JsonResponse
     * @throws GuzzleHttp\Exception\GuzzleException
     */
    public function get(string $city): JsonResponse
    {
        $openWeatherMapApiKey = $this->service->get('OPEN_WEATHER_API_KEY');
        if (!$openWeatherMapApiKey) {
            throw new ApiKeyNotFoundException('API key has not been provided');
        }

        try {
            $params = [
                'q'     => $city,
                'appid' => $openWeatherMapApiKey,
                'units' => 'metric',
            ];

            $openWeatherMapResponse = json_decode(
                $this->client->get(self::OPEN_WEATHER_MAP_BASE_URL, ['query' => $params])
                    ->getBody()
                    ->getContents()
            );
        } catch (GuzzleHttp\Exception\ClientException $exception) {
            // Rethrow the response from the open weather API if there was a non-200 response
            $responseContents = json_decode($exception->getResponse()->getBody()->getContents());

            return response()->json($responseContents, $exception->getCode());
        }

        // Because the API we are using is versioned, we can trust that the response is almost definitely going to have
        // the "main" and "temp" property.
        // I would use a factory if there was more complicated logic to create a weather instance. This is comparable to
        // creating a DateTimeImmutable object. I also did think to create an object that looked like the response, but I could
        // not see any value in this.
        return response()->json(new Entity((float)$openWeatherMapResponse->main->temp));
    }
}
