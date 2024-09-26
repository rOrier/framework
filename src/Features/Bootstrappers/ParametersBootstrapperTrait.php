<?php

namespace ROrier\Core\Features\Bootstrappers;

use Exception;
use ROrier\Config\Components\Bag;
use ROrier\Config\Interfaces\ParametersInterface;
use ROrier\Config\Services\Analyzer;
use ROrier\Config\Services\ConfigParsers\ArrayParameterParser;
use ROrier\Config\Services\ConfigParsers\ConstantParser;
use ROrier\Config\Services\ConfigParsers\EnvParser;
use ROrier\Config\Services\ConfigParsers\StringParameterParser;
use ROrier\Config\Services\DelayedProxies\ParametersProxy;
use ROrier\Config\Services\Parameters;
use ROrier\Core\Components\DataLoader;
use ROrier\Core\Interfaces\KernelInterface;

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
        if ($this->hasService('parameters')) {
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
            $this->getParametersData(),
            $this->getService('analyzer.config')
        );

        $this->getDelayedParameters()->setParameters($parameters);

        return $parameters;
    }

    protected function getParametersData(): Bag
    {
        /** @var KernelInterface $kernel */
        $kernel = $this->getService('kernel');

        $dataLoader = new DataLoader($kernel, $this);

        $data = $dataLoader->getData('getParametersConfigPath', 'parameters', $this->additionalParametersData);

        return new Bag($data);
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
}
