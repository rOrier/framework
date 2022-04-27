<?php

namespace ROrier\Core\Components\Bootstrappers;

use Exception;
use ROrier\Config\Interfaces\ParametersInterface;
use ROrier\Core\Foundations\AbstractBootstrapper;
use ROrier\Core\Interfaces\AppInterface;
use ROrier\Core\Interfaces\KernelInterface;
use ROrier\Container\Exceptions\ContainerException;
use ROrier\Container\Interfaces\ContainerInterface;

class AppBootstrapper extends AbstractBootstrapper
{
    private const DEFAULT_LOCAL_APP = 'ROrier\Core\Components\LocalApp';
    private const DEFAULT_GLOBAL_APP = 'ROrier\Core\Components\GlobalApp';

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
     * @param string $className
     * @return AppInterface
     * @throws ContainerException
     */
    public function buildLocalApp(string $className = self::DEFAULT_LOCAL_APP): AppInterface
    {
        $this->saveKnownServices();

        return new $className(
            $this->boot['root'],
            $this->getKernel(),
            $this->getParameters(),
            $this->getContainer()
        );
    }

    /**
     * @param string $className
     * @return AppInterface
     * @throws ContainerException
     */
    public function buildGlobalApp(string $className = self::DEFAULT_GLOBAL_APP): AppInterface
    {
        $this->saveKnownServices();

        $className::init(
            $this->boot['root'],
            $this->getKernel(),
            $this->getParameters(),
            $this->getContainer()
        );

        return $className::get();
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

    /**
     * @return KernelInterface
     */
    protected function getKernel(): KernelInterface
    {
        return $this->boot['kernel'];
    }

    /**
     * @return ContainerInterface
     */
    protected function getContainer(): ContainerInterface
    {
        return $this->boot['container'];
    }

    /**
     * @return ParametersInterface
     */
    protected function getParameters(): ParametersInterface
    {
        return $this->boot['parameters'];
    }
}
