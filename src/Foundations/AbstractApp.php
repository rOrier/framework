<?php

namespace ROrier\Core\Foundations;

use Exception;
use ROrier\Config\Interfaces\ParametersInterface;
use ROrier\Container\Interfaces\ContainerInterface;
use ROrier\Core\Interfaces\AppInterface;
use ROrier\Core\Interfaces\KernelInterface;

abstract class AbstractApp implements AppInterface
{
    protected string $root;

    protected KernelInterface $kernel;

    protected ParametersInterface $parameters;

    protected ContainerInterface $container;

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
