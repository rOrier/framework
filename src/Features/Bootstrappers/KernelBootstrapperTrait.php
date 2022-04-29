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
     * @return self
     * @throws Exception
     */
    public function buildKernel(): self
    {
        $kernel = new Kernel();

        $classNames = array_unique(array_merge($this->corePackages, $this->additionalPackages));

        foreach ($classNames as $className) {
            $package = new $className();
            $kernel->addPackage($package);
        }

        $this->boot['kernel'] = $kernel;

        return $this;
    }

    /**
     * @return KernelInterface
     * @throws Exception
     */
    protected function getKernel(): KernelInterface
    {
        if (!isset($this->boot['kernel'])) {
            throw new Exception("Kernel not found. Use buildKernel() to make it available.");
        }

        return $this->boot['kernel'];
    }
}
