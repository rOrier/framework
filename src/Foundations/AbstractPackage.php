<?php

namespace ROrier\Core\Foundations;

use ReflectionObject;
use ROrier\Core\Interfaces\ConfigLoaderInterface;
use ROrier\Core\Interfaces\PackageInterface;

abstract class AbstractPackage implements PackageInterface
{
    protected const CONFIG_LOADER = 'ROrier\Core\Components\ConfigLoaders\YamlLoader';

    public const PATH_PARAMETERS = '/../config/parameters';
    public const PATH_SERVICES = '/../config/services';

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

    protected function loadConfig($path): array
    {
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
        $path = realpath($this->getRoot() . static::PATH_PARAMETERS);

        return $this->loadConfig($path);
    }

    public function buildServices(): array
    {
        $path = realpath($this->getRoot() . static::PATH_SERVICES);

        return $this->loadConfig($path);
    }
}
