<?php

namespace ROrier\Core\Features\Bootstrappers;

use Exception;
use ROrier\Core\Components\Kernel;
use ROrier\Core\Interfaces\KernelInterface;
use ROrier\Core\Main;

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
        $kernel = $this->getKernel();

        $classNames = array_unique(array_merge(
            $this->config['packages'],
            $this->additionalPackages
        ));

        foreach ($classNames as $className) {
            $package = new $className();
            if (!$kernel->hasPackage($package->getName())) {
                $kernel->addPackage($package);
            }
        }

        return $kernel;
    }

    protected function getKernel(): KernelInterface
    {
        if (Main::ready() && !$this->config['kernel']['override']) {
            $kernel = Main::app()->getKernel();
        } else {
            $kernel = new Kernel();
        }

        return $kernel;
    }
}
