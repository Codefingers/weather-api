# Weather Service
This service provides a RESTful API for proxying to the Open Weather Map API.


## Getting started


### Prerequisites
Basic requirements: 

- PHP 7.4
- API Open Weather Map API key 


#### Open Weather Map API Key
Ensure you have an Open Weather Map API key set in your `.env` file. The environment key is in the environment 
distributable, only the value needs to be set. 
For more information see https://openweathermap.org/api


### Hosting locally
To run this project locally, run the following commands:

- `composer install`
- `php -S localhost:9292 -t public`


## Usage

To find out the weather for your city in a Celsius unit, send a request to the following endpoint:

- `localhost:9292/api/weather/{city}`

Example response for `http://localhost:9292/api/weather/london`

- `{"celsius":"18.87","fahrenheit":"65.97","kelvin":"292.02"}`


## Design
All non-framework related logic is held within the "Weather" domain.

As a side note, the environment service does not belong in the Weather domain and belongs somewhere like a generic area,
maybe in an `Environment` or similar namespace. 
