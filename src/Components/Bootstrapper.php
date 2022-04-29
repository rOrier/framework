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

    protected const APP_CLASS_NAME = 'ROrier\Core\Components\App';

    protected Boot $boot;

    protected array $services = [];

    protected array $builders = [];

    /**
     * Bootstrapper constructor.
     * @param Boot $boot
     * @throws Exception
     */
    public function __construct(Boot $boot)
    {
        $this->boot = $boot;

        $this->registerBuilders([
            'kernel' => 'buildKernel',
            'parameters' => 'buildParameters',
            'analyzer.config' => 'buildConfigAnalyzer',
            'library.services' => 'buildLibrary',
            'container' => 'buildContainer',
            'compilator.spec.services' => 'buildSpecCompilator',
            'factory.services' => 'buildServiceFactory',
            'builder.workbench.services' => 'buildServiceWorkbenchBuilder',
            'builder.service' => 'buildServiceBuilder',
            'analyzer.argument' => 'buildArgumentAnalyzer'
        ]);
    }

    /**
     * @param array $builders
     * @throws Exception
     */
    protected function registerBuilders(array $builders): void
    {
        foreach($builders as $name => $method) {
            $this->registerBuilder($name, $method);
        }
    }

    /**
     * @param string $name
     * @param string $method
     * @throws Exception
     */
    protected function registerBuilder(string $name, string $method): void
    {
        if (!method_exists($this, $method)) {
            throw new Exception("Builder method not found : '$method'.");
        }

        $this->builders[$name] = $method;
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
        if (!isset($this->builders[$name])) {
            throw new Exception("No builder found for requested service : '$name'.");
        }

        return call_user_func([$this, $this->builders[$name]]);
    }
}
