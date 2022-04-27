<?php

namespace ROrier\Core\Components;

use ROrier\Config\Interfaces\ParametersInterface;
use ROrier\Container\Interfaces\ContainerInterface;
use ROrier\Core\Foundations\AbstractApp;
use ROrier\Core\Interfaces\KernelInterface;

class LocalApp extends AbstractApp
{
    public function __construct(
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
}
