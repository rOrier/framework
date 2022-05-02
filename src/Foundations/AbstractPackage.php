<?php

namespace ROrier\Core\Foundations;

use Exception;
use ReflectionObject;
use ROrier\Config\Components\Bag;
use ROrier\Core\Interfaces\PackageInterface;

abstract class AbstractPackage implements PackageInterface
{
    private const DEFAULT_CONFIGURATION = [
        'config' => [
            'parameters' => [
                'path' => '/../config/parameters'
            ],
            'services' => [
                'path' => '/../config/services'
            ]
        ]
    ];

    protected const CUSTOM_CONFIGURATION = [];

    private string $root;

    private string $name;

    private Bag $config;

    public function __construct()
    {
        $this->config = new Bag(self::DEFAULT_CONFIGURATION);

        $this->config->merge(static::CUSTOM_CONFIGURATION);
    }

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
        return realpath($this->getRoot() . $this['config.parameters.path']);
    }

    /**
     * @inheritDoc
     */
    public function getServicesConfigPath(): string
    {
        return realpath($this->getRoot() . $this['config.services.path']);
    }

    // ###################################################################
    // ###       Array access
    // ###################################################################

    /**
     * @param mixed $offset
     * @param mixed $value
     * @throws Exception
     */
    public function offsetSet($offset, $value)
    {
        throw new Exception("Package configuration is read only.");
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->config[$offset]);
    }

    /**
     * @param mixed $offset
     * @throws Exception
     */
    public function offsetUnset($offset)
    {
        throw new Exception("Package configuration is read only.");
    }

    /**
     * @param mixed $offset
     * @return array|bool|mixed|null
     */
    public function offsetGet($offset)
    {
        return $this->config[$offset];
    }
}
