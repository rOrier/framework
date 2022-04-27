<?php

namespace ROrier\Core\Components;

use Exception;
use ROrier\Config\Interfaces\ParametersInterface;
use ROrier\Container\Interfaces\ContainerInterface;
use ROrier\Core\Interfaces\AppInterface;
use ROrier\Core\Interfaces\KernelInterface;

class App implements AppInterface
{
    private string $root;

    private KernelInterface $kernel;

    private ParametersInterface $parameters;

    private ContainerInterface $container;

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

    /**
     * @inheritDoc
     */
    public function getRoot(): string
    {
        return $this->root;
    }

    /**
     * @inheritDoc
     */
    public function getKernel(): KernelInterface
    {
        if (!isset($this->kernel)) {
            throw new Exception("No kernel defined.");
        }

        return $this->kernel;
    }

    /**
     * @inheritDoc
     */
    public function getParameter(string $address)
    {
        if (!isset($this->parameters)) {
            throw new Exception("No parameters defined.");
        }

        return $this->parameters[$address];
    }

    /**
     * @inheritDoc
     */
    public function getService(string $id): object
    {
        if (!isset($this->container)) {
            throw new Exception("No container defined.");
        }

        return $this->container->get($id);
    }
}
