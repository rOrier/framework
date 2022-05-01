<?php

namespace ROrier\Core\Foundations;

use ReflectionObject;
use ROrier\Core\Interfaces\PackageInterface;

abstract class AbstractPackage implements PackageInterface
{
    private string $root;

    private string $name;

    /**
     * @inheritDoc
     */
    public function getRoot(): string
    {
        if (!isset($this->root)) {
            $reflected = new ReflectionObject($this);
            $this->root = dirname($reflected->getFileName());
        }

        return $this->root;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        if (!isset($this->name)) {
            $pos = strrpos(static::class, '\\');
            $this->name = (false === $pos) ? static::class : substr(static::class, $pos + 1);
        }

        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getParametersConfigPath(): string
    {
        return realpath($this->getRoot() . static::PATH_PARAMETERS);
    }

    /**
     * @inheritDoc
     */
    public function getServicesConfigPath(): string
    {
        return realpath($this->getRoot() . static::PATH_SERVICES);
    }
}
