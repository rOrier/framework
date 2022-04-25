<?php

namespace ROrier\Core\Components;

use Exception;
use ROrier\Config\Interfaces\ParametersInterface;
use ROrier\Container\Interfaces\ContainerInterface;

class App
{
    private string $root;

    private Kernel $kernel;

    private ParametersInterface $parameters;

    private ContainerInterface $container;

    public function __construct(string $root)
    {
        $this->root = $root;
    }

    /**
     * @param Kernel $kernel
     * @return self
     */
    public function setKernel(Kernel $kernel): self
    {
        $this->kernel = $kernel;

        return $this;
    }

    /**
     * @param ParametersInterface $parameters
     * @return self
     */
    public function setParameters(ParametersInterface $parameters): self
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * @param ContainerInterface $container
     * @return self
     */
    public function setContainer(ContainerInterface $container): self
    {
        $this->container = $container;

        return $this;
    }

    /**
     * @return string
     */
    public function getRoot(): string
    {
        return $this->root;
    }

    /**
     * @return Kernel
     * @throws Exception
     */
    public function getKernel(): Kernel
    {
        if (!isset($this->kernel)) {
            throw new Exception("No kernel defined.");
        }

        return $this->kernel;
    }

    /**
     * @param string $address
     * @return mixed
     * @throws Exception
     */
    public function getParameter(string $address)
    {
        if (!isset($this->parameters)) {
            throw new Exception("No parameters defined.");
        }

        return $this->parameters[$address];
    }

    /**
     * @param string $id
     * @return object
     * @throws Exception
     */
    public function getService(string $id): object
    {
        if (!isset($this->container)) {
            throw new Exception("No container defined.");
        }

        return $this->container->get($id);
    }
}
