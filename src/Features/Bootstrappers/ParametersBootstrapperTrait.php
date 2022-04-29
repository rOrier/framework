<?php

namespace ROrier\Core\Features\Bootstrappers;

use Exception;
use ROrier\Config\Components\Bag;
use ROrier\Config\Interfaces\AnalyzerInterface;
use ROrier\Config\Interfaces\ParametersInterface;
use ROrier\Config\Services\Analyzer;
use ROrier\Config\Services\ConfigParsers\ArrayParameterParser;
use ROrier\Config\Services\ConfigParsers\ConstantParser;
use ROrier\Config\Services\ConfigParsers\EnvParser;
use ROrier\Config\Services\ConfigParsers\StringParameterParser;
use ROrier\Config\Services\DelayedProxies\ParametersProxy;
use ROrier\Config\Services\Parameters;
use ROrier\Core\Interfaces\PackageInterface;

trait ParametersBootstrapperTrait
{
    private array $additionalParametersData = array();

    /**
     * @param array $data
     * @return self
     * @throws Exception
     */
    public function addParametersData(array $data): self
    {
        if (isset($this->boot['parameters'])) {
            throw new Exception("Parameters already built. Add parameters data before using buildParameters().");
        }

        $this->additionalParametersData[] = $data;

        return $this;
    }

    /**
     * @return ParametersInterface
     * @throws Exception
     */
    protected function buildParameters(): ParametersInterface
    {
        $parameters = new Parameters(
            $this->buildParametersData(),
            $this->getConfigAnalyzer()
        );

        $this->getDelayedParameters()->setParameters($parameters);

        return $parameters;
    }

    /**
     * @return Bag
     */
    protected function buildParametersData()
    {
        $data = new Bag();

        /** @var PackageInterface $package */
        foreach ($this->getKernel()->getPackages() as $package) {
            $data->merge($package->buildParameters());
        }

        foreach ($this->additionalParametersData as $additionalData) {
            $data->merge($additionalData);
        }

        return $data;
    }

    /**
     * @return Analyzer
     */
    protected function buildConfigAnalyzer(): Analyzer
    {
        return new Analyzer([
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

    /**
     * @return ParametersInterface
     * @throws Exception
     */
    protected function getParameters(): ParametersInterface
    {
        return $this->getService('parameters');
    }

    /**
     * @return AnalyzerInterface
     * @throws Exception
     */
    protected function getConfigAnalyzer(): AnalyzerInterface
    {
        return $this->getService('analyzer.config');
    }
}
