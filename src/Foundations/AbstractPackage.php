<?php

namespace ROrier\Core\Foundations;

use ReflectionObject;
use ROrier\Core\Interfaces\ConfigLoaderInterface;
use ROrier\Core\Interfaces\PackageInterface;

abstract class AbstractPackage implements PackageInterface
{
    protected const CONFIG_LOADER = 'ROrier\Core\Components\ConfigLoaders\YamlLoader';

    private string $root;

    private string $name;

    private ConfigLoaderInterface $configLoader;

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

    protected function getConfigLoader(): ConfigLoaderInterface
    {
        if (!isset($this->configLoader)) {
            $configLoaderClassName = static::CONFIG_LOADER;
            $this->configLoader = new $configLoaderClassName();
        }

        return $this->configLoader;
    }

    protected function loadConfig($type): array
    {
        $path = $this->getConfigPath() . DIRECTORY_SEPARATOR . $type;

        return is_dir($path) ? $this->getConfigLoader()->load($path) : [];
    }

    /**
     * @inheritDoc
     */
    public function getConfigPath(): string
    {
        return realpath($this->getRoot() . DIRECTORY_SEPARATOR . '_config');
    }

    public function buildParameters(): array
    {
        return $this->loadConfig('parameters');
    }

    public function buildServices(): array
    {
        return $this->loadConfig('services');
    }
}
