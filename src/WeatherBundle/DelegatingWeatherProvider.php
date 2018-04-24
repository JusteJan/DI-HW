<?php

namespace Nfq\WeatherBundle;


class DelegatingWeatherProvider implements WeatherProviderInterface
{
    private $providers;

    public function __construct(array $providers)
    {
        $this->providers = $providers;
    }

    public function fetch(Location $location): Weather
    {
        foreach ($this->providers as $provider) {
            try {
                return $provider->fetch($location);
            } catch (WeatherProviderException $e) {
                //Catch the exception from a provider and go on to the next provider
            }
        }
        throw new WeatherProviderException("Could not get current weather data from weather providers.");
    }
}