<?php

namespace ROrier\Core\Components;

use Exception;
use ROrier\Core\Features\Bootstrappers\AppBootstrapperTrait;
use ROrier\Core\Features\Bootstrappers\ContainerBootstrapperTrait;
use ROrier\Core\Features\Bootstrappers\KernelBootstrapperTrait;
use ROrier\Core\Features\Bootstrappers\LibraryBootstrapperTrait;
use ROrier\Core\Features\Bootstrappers\ParametersBootstrapperTrait;

class Bootstrapper
{
    use KernelBootstrapperTrait,
        ParametersBootstrapperTrait,
        LibraryBootstrapperTrait,
        ContainerBootstrapperTrait,
        AppBootstrapperTrait;

    protected const DEFAULT_BUILDERS_CONFIGURATION = [
        'app' => 'buildApp',
        'kernel' => 'buildKernel',
        'parameters' => 'buildParameters',
        'analyzer.config' => 'buildConfigAnalyzer',
        'analyzer.argument' => 'buildArgumentAnalyzer',
        'library.services' => 'buildLibrary',
        'container' => 'buildContainer',
        'compilator.spec.services' => 'buildSpecCompilator',
        'factory.services' => 'buildServiceFactory',
        'builder.service' => 'buildServiceBuilder',
        'builder.workbench.services' => 'buildServiceWorkbenchBuilder'
    ];

    protected const DEFAULT_CONFIGURATION = [
        'root' => null,
        'main_class_name' => 'ROrier\Core\Main',
        'app_class_name' => 'ROrier\Core\Components\App',
        'builders' => self::DEFAULT_BUILDERS_CONFIGURATION
    ];

    protected array $services = [];

    protected array $config;

    protected array $requestedServiceBuilding = [];

    /**
     * Bootstrapper constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = array_merge(self::DEFAULT_CONFIGURATION, $config);
    }

    /**
     * @param array $data
     */
    protected function config(array $data): void
    {
        $this->config = array_merge($this->config, $data);
    }

    /**
     * @param string $name
     * @return object
     * @throws Exception
     */
    protected function getService(string $name): object
    {
        if (!$this->hasService($name)) {
            $this->services[$name] = $this->callServiceBuilder($name);
        }

        return $this->services[$name];
    }

    /**
     * @param string $name
     * @return bool
     */
    protected function hasService(string $name): bool
    {
        return isset($this->services[$name]);
    }

    /**
     * @param string $name
     * @return object
     * @throws Exception
     */
    protected function callServiceBuilder(string $name): object
    {
        $builders = $this->config['builders'];

        if (!isset($builders[$name])) {
            throw new Exception("No builder found for requested service : '$name'.");
        } elseif (in_array($name, $this->requestedServiceBuilding)) {
            throw new Exception("Circular reference detected !! Service building already requested : '$name'.");
        }

        $this->requestedServiceBuilding[] = $name;

        return call_user_func([$this, $builders[$name]]);
    }
}
