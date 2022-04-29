<?php

namespace ROrier\Core\Components;

use Exception;
use ROrier\Config\Components\Bag;
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
        'analyzer' => [
            'config' => 'buildConfigAnalyzer',
            'argument' => 'buildArgumentAnalyzer'
        ],
        'library' => [
            'services' => 'buildLibrary'
        ],
        'container' => 'buildContainer',
        'compilator' => [
            'spec' => [
                'services' => 'buildSpecCompilator'
            ],
        ],
        'factory' => [
            'services' => 'buildServiceFactory'
        ],
        'builder' => [
            'service' => 'buildServiceBuilder',
            'workbench' => [
                'services' => 'buildServiceWorkbenchBuilder'
            ]
        ]
    ];

    protected const DEFAULT_CONFIGURATION = [
        'app_class_name' => 'ROrier\Core\Components\App',
        'builders' => self::DEFAULT_BUILDERS_CONFIGURATION
    ];

    protected Boot $boot;

    protected array $services = [];

    protected Bag $config;

    /**
     * Bootstrapper constructor.
     * @param Boot $boot
     * @throws Exception
     */
    public function __construct(Boot $boot)
    {
        $this->boot = $boot;

        $this->config = new Bag(self::DEFAULT_CONFIGURATION);
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
        if (!isset($this->config["builders.$name"])) {
            throw new Exception("No builder found for requested service : '$name'.");
        }

        return call_user_func([$this, $this->config["builders.$name"]]);
    }
}
