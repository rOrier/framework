<?php

namespace ROrier\Core\Components\Bootstrappers;

use ROrier\Config\Components\Bag;
use ROrier\Config\Services\Analyzer;
use ROrier\Config\Services\ConfigParsers\ArrayParameterParser;
use ROrier\Config\Services\ConfigParsers\ConstantParser;
use ROrier\Config\Services\ConfigParsers\EnvParser;
use ROrier\Config\Services\ConfigParsers\StringParameterParser;
use ROrier\Config\Services\DelayedProxies\ParametersProxy;
use ROrier\Config\Services\Parameters;
use ROrier\Core\Foundations\AbstractBootstrapper;
use ROrier\Core\Interfaces\KernelInterface;
use ROrier\Core\Interfaces\PackageInterface;

class ParametersBootstrapper extends AbstractBootstrapper
{
    private array $additionalData = array();

    private ParametersProxy $delayedParameters;

    /**
     * @param array $data
     * @return self
     */
    public function addParametersData(array $data): self
    {
        $this->additionalData[] = $data;

        return $this;
    }

    public function buildParameters(): LibraryBootstrapper
    {
        $this->boot['parameters'] = new Parameters(
            $this->buildData(),
            $this->buildConfigAnalyzer()
        );

        $this->getDelayedParameters()->setParameters($this->boot['parameters']);

        return new LibraryBootstrapper($this->boot);
    }

    protected function buildData()
    {
        $data = new Bag();

        /** @var PackageInterface $package */
        foreach ($this->getKernel()->getPackages() as $package) {
            $data->merge($package->buildParameters());
        }

        foreach ($this->additionalData as $additionalData) {
            $data->merge($additionalData);
        }

        return $data;
    }

    protected function buildConfigAnalyzer(): Analyzer
    {
        return $this->boot['analyzer.config'] = new Analyzer([
            new ConstantParser(),
            new EnvParser(),
            new StringParameterParser($this->getDelayedParameters()),
            new ArrayParameterParser($this->getDelayedParameters())
        ]);
    }

    /**
     * @return ParametersProxy
     */
    protected function getDelayedParameters(): ParametersProxy
    {
        static $delayedParameters = null;

        if ($delayedParameters === null) {
            $delayedParameters = new ParametersProxy();
        }

        return $delayedParameters;
    }

    protected function getKernel(): KernelInterface
    {
        return $this->boot['kernel'];
    }
}
