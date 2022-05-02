<?php

namespace ROrier\Core\Features\Bootstrappers;

use Exception;
use ROrier\Core\Components\Kernel;
use ROrier\Core\Interfaces\ConfigLoaderInterface;
use ROrier\Core\Interfaces\KernelInterface;
use ROrier\Core\Interfaces\PackageInterface;

trait KernelBootstrapperTrait
{
    private array $additionalPackages = [];

    /**
     * @param string[] $userPackages
     * @return self
     */
    public function addPackages(array $userPackages): self
    {
        $this->additionalPackages = array_merge(
            $this->additionalPackages,
            $userPackages
        );

        return $this;
    }

    /**
     * @return KernelInterface
     * @throws Exception
     */
    protected function buildKernel(): KernelInterface
    {
        $kernel = new Kernel();

        $classNames = array_unique(array_merge(
            $this->config['packages'],
            $this->additionalPackages
        ));

        foreach ($classNames as $className) {
            $package = new $className();
            $kernel->addPackage($package);
        }

        return $kernel;
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
}
