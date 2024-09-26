<?php

namespace ROrier\Core\Components;

use Exception;
use ROrier\Config\Tools\CollectionTool;
use ROrier\Core\Interfaces\ConfigLoaderInterface;
use ROrier\Core\Interfaces\KernelInterface;
use ROrier\Core\Interfaces\PackageInterface;

class DataLoader
{
    private KernelInterface $kernel;

    private Bootstrapper $bootstrapper;

    protected array $configLoaders = [];

    public function __construct(KernelInterface $kernel, Bootstrapper $bootstrapper)
    {
        $this->kernel = $kernel;
        $this->bootstrapper = $bootstrapper;
    }

    public function getData(string $getPathMethod, string $cacheFile, array $additionalDataSet = []): array
    {
        if ($this->bootstrapper->getCacheFolder()) {
            $src = $this->bootstrapper->getCacheFolder() . DIRECTORY_SEPARATOR . $cacheFile . '.json';
            if (is_file($src)) {
                $jsonData = json_decode(file_get_contents($src), true);
                if (is_array($jsonData)) {
                    return $jsonData;
                }
            }
        }

        $data = $this->loadData($getPathMethod);

        foreach ($additionalDataSet as $additionalData) {
            CollectionTool::merge($data, $additionalData);
        }

        if ($this->bootstrapper->getCacheFolder()) {
            $src = $this->bootstrapper->getCacheFolder() . DIRECTORY_SEPARATOR . $cacheFile . '.json';
            file_put_contents($src, json_encode($data));
        }

        return $data;
    }

    protected function loadData(string $getPathMethod): array
    {
        $data = [];

        /** @var PackageInterface $package */
        foreach ($this->kernel->getPackages() as $package) {
            $path = call_user_func([$package, $getPathMethod]);

            if (!empty($path) && is_dir($path)) {
                /** @var ConfigLoaderInterface $configLoader */
                $configLoader = $this->getPackageLoader($package);

                CollectionTool::merge($data, $configLoader->load($path));
            }
        }

        return $data;
    }

    /**
     * @param PackageInterface $package
     * @return ConfigLoaderInterface
     * @throws Exception
     */
    protected function getPackageLoader(PackageInterface $package): ConfigLoaderInterface
    {
        if ($package['loader.class']) {
            $className = $package['loader.class'];
        } else {
            switch($package['loader.type']) {
                case 'yaml':
                case 'yml':
                    $className = 'ROrier\Core\Components\ConfigLoaders\YamlLoader';
                    break;
                case 'json':
                    $className = 'ROrier\Core\Components\ConfigLoaders\JsonLoader';
                    break;
                default:
                    throw new Exception("Unknown loader type : '{$package['loader.type']}'.");
            }
        }

        return $this->getConfigLoader($className);
    }

    /**
     * @param string $className
     * @return ConfigLoaderInterface
     */
    protected function getConfigLoader(string $className): ConfigLoaderInterface
    {
        if (!isset($this->configLoaders[$className])) {
            $this->configLoaders[$className] = new $className();
        }

        return $this->configLoaders[$className];
    }
}