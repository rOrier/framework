<?php

namespace ROrier\Core\Components;

use Exception;
use ROrier\Config\Interfaces\ParametersInterface;
use ROrier\Container\Interfaces\ContainerInterface;
use ROrier\Core\Foundations\AbstractApp;
use ROrier\Core\Interfaces\AppInterface;
use ROrier\Core\Interfaces\KernelInterface;

class GlobalApp extends AbstractApp
{
    static private self $instance;

    private function __construct(
        string $root,
        KernelInterface $kernel,
        ParametersInterface $parameters,
        ContainerInterface $container
    ) {
        $this->root = $root;
        $this->kernel = $kernel;
        $this->parameters = $parameters;
        $this->container = $container;
    }

    /**
     * @param string $root
     * @param KernelInterface $kernel
     * @param ParametersInterface $parameters
     * @param ContainerInterface $container
     * @throws Exception
     */
    static public function init(
        string $root,
        KernelInterface $kernel,
        ParametersInterface $parameters,
        ContainerInterface $container
    ) {
        if (isset(self::$instance)) {
            throw new Exception("App component already initialized");
        }

        self::$instance = new self($root, $kernel, $parameters, $container);
    }

    /**
     * @return AppInterface
     * @throws Exception
     */
    static public function getInstance()
    {
        if (!isset(self::$instance)) {
            throw new Exception("App component never initialized");
        }

        return self::$instance;
    }
}
