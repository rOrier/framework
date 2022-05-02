<?php

namespace ROrier\Core\Foundations;

use Exception;
use ReflectionObject;
use ROrier\Config\Components\Bag;
use ROrier\Core\Interfaces\PackageInterface;

abstract class AbstractPackage implements PackageInterface
{
    private const DEFAULT_CONFIGURATION = [
        'name' => null,
        'root' => null,
        'loader' => [
            'class' => null,
            'type' => 'yaml'
        ],
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

    private Bag $config;

    public function __construct()
    {
        $this->config = new Bag(self::DEFAULT_CONFIGURATION);

        $this->config->merge(static::CUSTOM_CONFIGURATION);

        if (!$this['root']) {
            $this->config->merge([
                'root' => $this->buildRoot()
            ]);
        }

        if (!$this['name']) {
            $this->config->merge([
                'name' => $this->buildName()
            ]);
        }
    }

    /**
     * @return string
     */
    protected function buildRoot()
    {
        $reflected = new ReflectionObject($this);

        return dirname($reflected->getFileName());
    }

    /**
     * @inheritDoc
     */
    public function getRoot(): string
    {
        return $this['root'];
    }

    /**
     * @return string
     */
    public function buildName(): string
    {
        $pos = strrpos(static::class, '\\');

        return ($pos === false) ? static::class : substr(static::class, $pos + 1);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this['name'];
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
