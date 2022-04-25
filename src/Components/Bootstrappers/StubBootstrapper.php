<?php

namespace ROrier\Core\Components\Bootstrappers;

use ROrier\Config\Interfaces\ParametersInterface;
use ROrier\Core\Components\App;
use ROrier\Core\Foundations\AbstractBootstrapper;
use ROrier\Core\Interfaces\KernelInterface;
use ROrier\Services\Exceptions\ContainerException;
use ROrier\Services\Interfaces\ContainerInterface;

class StubBootstrapper extends AbstractBootstrapper
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
     * @return App
     * @throws ContainerException
     */
    public function finalyze(): App
    {
        $this->saveKnownServices();

        return $this->getApp()
            ->setKernel($this->boot['kernel'])
            ->setContainer($this->boot['container'])
            ->setParameters($this->boot['parameters'])
        ;
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

    protected function getApp(): App
    {
        return $this->boot['app'];
    }
}
