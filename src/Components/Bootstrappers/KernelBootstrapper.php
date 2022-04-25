<?php

namespace ROrier\Core\Components\Bootstrappers;

use Exception;
use ROrier\Config\ConfigPackage;
use ROrier\Core\Components\Kernel;
use ROrier\Core\CorePackage;
use ROrier\Core\Foundations\AbstractBootstrapper;
use ROrier\Core\Interfaces\PackageInterface;
use ROrier\Services\ContainerPackage;

class KernelBootstrapper extends AbstractBootstrapper
{
    /**
     * @param PackageInterface[] $packages
     * @return ParametersBootstrapper
     * @throws Exception
     */
    public function buildKernel(array $packages): ParametersBootstrapper
    {
        $kernel = (new Kernel())
            ->addPackage(new CorePackage())
            ->addPackage(new ContainerPackage())
            ->addPackage(new ConfigPackage())
        ;

        foreach ($packages as $package) {
            $kernel->addPackage($package);
        }

        $this->boot['kernel'] = $kernel;

        return new ParametersBootstrapper($this->boot);
    }
}
