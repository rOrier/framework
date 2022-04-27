<?php

namespace ROrier\Core\Components;

use ROrier\Config\Interfaces\ParametersInterface;
use ROrier\Container\Interfaces\ContainerInterface;
use ROrier\Core\Foundations\AbstractApp;
use ROrier\Core\Interfaces\KernelInterface;

class App extends AbstractApp
{
    public function __construct(string $root)
    {
        $this->root = $root;
    }

    /**
     * @inheritDoc
     */
    public function setKernel(KernelInterface $kernel): self
    {
        $this->kernel = $kernel;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setParameters(ParametersInterface $parameters): self
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setContainer(ContainerInterface $container): self
    {
        $this->container = $container;

        return $this;
    }
}
