<?php

namespace Nfq\WeatherBundle;

class OpenWeatherMapWeatherProvider implements WeatherProviderInterface
{
    private $apiKey;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * {@inheritdoc}
     */
    public function fetch(Location $location): Weather
    {
        $lat = $location->getLat();
        $lon = $location->getLon();

        if (!isset($this->apiKey)){
            throw new WeatherProviderException("No API key provided.\n");
        }

        $data = $this->getData($lat, $lon);

        $temperature = $this->parseData($data);

        $weather = new Weather();
        $weather->setTemperature($temperature);

        return $weather;
    }

    public function getData($lat, $lon)
    {
        $baseUrl = 'http://api.openweathermap.org/data/2.5/weather?';
        $query = "lat=$lat&lon=$lon&units=metric&APPID=$this->apiKey";
        $queryUrl = $baseUrl . $query;
        $dataJson = @file_get_contents($queryUrl);

        if (strpos($http_response_header[0], "200 OK") === false) {
            throw new WeatherProviderException("Could not get current weather data from OpenWeatherMap.\n");
        }

        $data = json_decode($dataJson);

        return $data;

    }

    public function parseData($data)
    {
        if (!isset($data->main->temp)) {
            throw new WeatherProviderException("Could not get current weather data from OpenWeatherMap.\n");
        }

        $temperature = $data->main->temp;

        return $temperature;
    }
}
