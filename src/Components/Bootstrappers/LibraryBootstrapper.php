<?php

namespace ROrier\Core\Components\Bootstrappers;

use ROrier\Config\Tools\CollectionTool;
use ROrier\Core\Foundations\AbstractBootstrapper;
use ROrier\Core\Interfaces\KernelInterface;
use ROrier\Core\Interfaces\PackageInterface;
use ROrier\Container\Services\Compilator;
use ROrier\Container\Services\Libraries\ServiceLibrary;
use ROrier\Container\Services\ServiceSpecCompilers\FactoryCompiler;
use ROrier\Container\Services\ServiceSpecCompilers\InheritanceCompiler;

class LibraryBootstrapper extends AbstractBootstrapper
{
    private array $additionalData = array();

    /**
     * @param array $data
     * @return self
     */
    public function addServicesData(array $data): self
    {
        $this->additionalData[] = $data;

        return $this;
    }

    public function buildLibrary(): ContainerBootstrapper
    {
        $this->boot['library.services'] = new ServiceLibrary(
            $this->buildData(),
            $this->buildSpecCompilator()
        );

        return new ContainerBootstrapper($this->boot);
    }

    protected function buildData()
    {
        $data = [];

        /** @var PackageInterface $package */
        foreach ($this->getKernel()->getPackages() as $package) {
            CollectionTool::merge($data, $package->buildServices());
        }

        foreach ($this->additionalData as $additionalData) {
            CollectionTool::merge($data, $additionalData);
        }

        return $data;
    }

    protected function buildSpecCompilator()
    {
        return $this->boot['compilator.spec.services'] = new Compilator([
            new InheritanceCompiler(),
            new FactoryCompiler()
        ]);
    }

    protected function getKernel(): KernelInterface
    {
        return $this->boot['kernel'];
    }
}
