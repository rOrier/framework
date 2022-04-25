<?php

namespace ROrier\Core\Components;

use Exception;
use ROrier\Config\Interfaces\ParametersInterface;
use ROrier\Services\Interfaces\ContainerInterface;

class App
{
    private string $root;

    private ParametersInterface $parameters;

    private ContainerInterface $container;

    public function __construct(string $root)
    {
        $this->root = $root;
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
