<?php

namespace ROrier\Core\Components\Bootstrappers;

use ROrier\Config\Tools\CollectionTool;
use ROrier\Core\Foundations\AbstractBootstrapper;
use ROrier\Services\Services\Compilator;
use ROrier\Services\Services\Libraries\ServiceLibrary;
use ROrier\Services\Services\ServiceSpecCompilers\FactoryCompiler;
use ROrier\Services\Services\ServiceSpecCompilers\InheritanceCompiler;

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

        $this->preloadData($data, 'fixed.json');
        $this->preloadData($data, 'abstract.json');

        foreach ($this->additionalData as $additionalData) {
            CollectionTool::merge($data, $additionalData);
        }

        return $data;
    }

    protected function preloadData(array &$data, $filename)
    {
        $path = realpath(__DIR__ . "/../../../../services/config/services/$filename");

        CollectionTool::merge($data, json_decode(file_get_contents($path), true));
    }

    protected function buildSpecCompilator()
    {
        return $this->boot['compilator.spec.services'] = new Compilator([
            new InheritanceCompiler(),
            new FactoryCompiler()
        ]);
    }
}
