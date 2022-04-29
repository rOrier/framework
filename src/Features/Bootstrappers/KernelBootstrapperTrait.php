<?php

namespace ROrier\Core\Features\Bootstrappers;

use Exception;
use ROrier\Config\ConfigPackage;
use ROrier\Core\Components\Kernel;
use ROrier\Core\CorePackage;
use ROrier\Container\ContainerPackage;
use ROrier\Core\Interfaces\KernelInterface;

trait KernelBootstrapperTrait
{
    private array $corePackages = [
        CorePackage::class,
        ContainerPackage::class,
        ConfigPackage::class
    ];

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

        $classNames = array_unique(array_merge($this->corePackages, $this->additionalPackages));

        foreach ($classNames as $className) {
            $package = new $className();
            $kernel->addPackage($package);
        }

        return $kernel;
    }

    /**
     * @return KernelInterface
     * @throws Exception
     */
    protected function getKernel(): KernelInterface
    {
        return $this->getService('kernel');
    }
}
