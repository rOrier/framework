<?php

namespace ROrier\Core\Components;

use Exception;
use ROrier\Config\ConfigPackage;
use ROrier\Config\Tools\CollectionTool;
use ROrier\Container\ContainerPackage;
use ROrier\Core\CorePackage;
use ROrier\Core\Features\Bootstrappers\AppBootstrapperTrait;
use ROrier\Core\Features\Bootstrappers\ContainerBootstrapperTrait;
use ROrier\Core\Features\Bootstrappers\KernelBootstrapperTrait;
use ROrier\Core\Features\Bootstrappers\LibraryBootstrapperTrait;
use ROrier\Core\Features\Bootstrappers\ParametersBootstrapperTrait;
use ROrier\Core\Interfaces\ConfigLoaderInterface;
use ROrier\Core\Main;

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

    protected const DEFAULT_PACKAGES = [
        CorePackage::class,
        ContainerPackage::class,
        ConfigPackage::class
    ];

    protected const DEFAULT_CONFIGURATION = [
        'root' => null,
        'main_class_name' => Main::class,
        'app_class_name' => App::class,
        'packages' => self::DEFAULT_PACKAGES,
        'builders' => self::DEFAULT_BUILDERS_CONFIGURATION
    ];

    protected const CUSTOM_CONFIGURATION = [];

    protected array $services = [];

    protected array $config = self::DEFAULT_CONFIGURATION;

    protected array $requestedServiceBuilding = [];

    protected array $configLoaders = [];

    /**
     * Bootstrapper constructor.
     * @param array $runtimeConfiguration
     */
    public function __construct(array $runtimeConfiguration = [])
    {
        CollectionTool::merge($this->config, static::CUSTOM_CONFIGURATION);
        CollectionTool::merge($this->config, $runtimeConfiguration);
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

    /**
     * @param string $className
     * @return ConfigLoaderInterface
     */
    protected function getConfigLoader(string $className): ConfigLoaderInterface
    {
        if (!isset($this->configLoaders[$className])) {
            $this->configLoaders[$className] = new $className();
        }

        return $this->configLoaders[$className];
    }
}
