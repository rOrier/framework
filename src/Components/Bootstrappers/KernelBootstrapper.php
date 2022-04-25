<?php

namespace ROrier\Core\Components\Bootstrappers;

use Exception;
use ROrier\Core\Components\Kernel;
use ROrier\Core\Foundations\AbstractBootstrapper;
use ROrier\Core\Interfaces\PackageInterface;

class KernelBootstrapper extends AbstractBootstrapper
{
    /**
     * @param PackageInterface[] $packages
     * @return ParametersBootstrapper
     * @throws Exception
     */
    public function buildKernel(array $packages): ParametersBootstrapper
    {
        $kernel = new Kernel();

        foreach ($packages as $package) {
            $kernel->addPackage($package);
        }

        $this->boot['kernel'] = $kernel;

        return new ParametersBootstrapper($this->boot);
    }
}
