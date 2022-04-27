<?php

namespace ROrier\Core\Components\Bootstrappers;

use Exception;
use ROrier\Config\Interfaces\ParametersInterface;
use ROrier\Core\Components\GlobalApp;
use ROrier\Core\Components\LocalApp;
use ROrier\Core\Foundations\AbstractBootstrapper;
use ROrier\Core\Interfaces\AppInterface;
use ROrier\Core\Interfaces\KernelInterface;
use ROrier\Container\Exceptions\ContainerException;
use ROrier\Container\Interfaces\ContainerInterface;

class AppBootstrapper extends AbstractBootstrapper
{
    private array $knownServices = [
        'container',
        'parameters',
        'library.services',
        'factory.services',
        'builder.workbench.services',
        'builder.service',
        'analyzer.config',
        'analyzer.argument',
        'compilator.spec.services'
    ];

    /**
     * @return AppInterface
     * @throws ContainerException
     */
    public function buildLocalApp(): AppInterface
    {
        $this->saveKnownServices();

        return new LocalApp(
            $this->boot['root'],
            $this->getKernel(),
            $this->getParameters(),
            $this->getContainer()
        );
    }

    /**
     * @return AppInterface
     * @throws ContainerException
     * @throws Exception
     */
    public function buildGlobalApp(): AppInterface
    {
        $this->saveKnownServices();

        GlobalApp::init(
            $this->boot['root'],
            $this->getKernel(),
            $this->getParameters(),
            $this->getContainer()
        );

        return GlobalApp::getInstance();
    }

    /**
     * @throws ContainerException
     */
    protected function saveKnownServices()
    {
        $container = $this->getContainer();

        foreach ($this->knownServices as $knownService) {
            if (isset($this->boot[$knownService])) {
                $container->setService($knownService, $this->boot[$knownService]);
            }
        }
    }

    protected function getKernel(): KernelInterface
    {
        return $this->boot['kernel'];
    }

    protected function getContainer(): ContainerInterface
    {
        return $this->boot['container'];
    }

    protected function getParameters(): ParametersInterface
    {
        return $this->boot['parameters'];
    }
}
