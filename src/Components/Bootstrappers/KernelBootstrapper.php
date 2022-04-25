<?php

namespace ROrier\Core\Components\Bootstrappers;

use Exception;
use ROrier\Config\ConfigPackage;
use ROrier\Core\Components\Kernel;
use ROrier\Core\CorePackage;
use ROrier\Core\Foundations\AbstractBootstrapper;
use ROrier\Core\Interfaces\PackageInterface;
use ROrier\Container\ContainerPackage;

class KernelBootstrapper extends AbstractBootstrapper
{
    private array $coreClassNames = [
        CorePackage::class,
        ContainerPackage::class,
        ConfigPackage::class
    ];

    /**
     * @param string[] $userClassNames
     * @return ParametersBootstrapper
     * @throws Exception
     */
    public function buildKernel(array $userClassNames): ParametersBootstrapper
    {
        $kernel = new Kernel();

        $classNames = array_merge($this->coreClassNames, $userClassNames);

        foreach ($classNames as $className) {
            $package = new $className();
            $kernel->addPackage($package);
        }

        $this->boot['kernel'] = $kernel;

        return new ParametersBootstrapper($this->boot);
    }
}
