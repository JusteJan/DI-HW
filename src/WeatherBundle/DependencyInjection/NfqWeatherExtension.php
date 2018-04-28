<?php

namespace Nfq\WeatherBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Nfq\WeatherBundle\WeatherProviderInterface;
use Symfony\Component\DependencyInjection\Reference;

class NfqWeatherExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('providers.yaml');

        $container->setAlias(WeatherProviderInterface::class, 'nfq_weather.provider.' . $config['provider']);

        $apiKey = $config['providers']['openweathermap']['api_key'];

        $openweather = $container->getDefinition('nfq_weather.provider.openweathermap');
        $openweather->replaceArgument(0, $apiKey);


        $providerList = $config['providers']['delegating']['providers'];

        $providers = [];

        $delegating = $container->getDefinition('nfq_weather.provider.delegating');

        foreach($providerList as $provider) {
            $providers[] = new Reference('nfq_weather.provider.' . $provider);
            }

        $delegating->replaceArgument(0, $providers);
    }
}
