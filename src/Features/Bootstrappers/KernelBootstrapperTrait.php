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
    private array $coreClassNames = [
        CorePackage::class,
        ContainerPackage::class,
        ConfigPackage::class
    ];

    /**
     * @param string[] $userClassNames
     * @return self
     * @throws Exception
     */
    public function buildKernel(array $userClassNames): self
    {
        $kernel = new Kernel();

        $classNames = array_unique(array_merge($this->coreClassNames, $userClassNames));

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
