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
use ROrier\Core\Interfaces\ConfigLoaderInterface;
use ROrier\Core\Interfaces\KernelInterface;
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
        if ($this->getCacheFolder()) {
            $src = $this->getCacheFolder() . DIRECTORY_SEPARATOR . 'parameters.json';
            if (is_file($src)) {
                $jsonData = json_decode(file_get_contents($src), true);
                if (is_array($jsonData)) {
                    return new Bag($jsonData);
                }
            }
        }

        $data = $this->buildParametersData();

        if ($this->getCacheFolder()) {
            $src = $this->getCacheFolder() . DIRECTORY_SEPARATOR . 'parameters.json';
            file_put_contents($src, json_encode($data->toArray()));
        }

        return $data;
    }

    /**
     * @return Bag
     */
    protected function buildParametersData(): Bag
    {
        $data = new Bag();

        /** @var KernelInterface $kernel */
        $kernel = $this->getService('kernel');

        /** @var PackageInterface $package */
        foreach ($kernel->getPackages() as $package) {
            $path = $package->getParametersConfigPath();

            if (!empty($path) && is_dir($path)) {
                /** @var ConfigLoaderInterface $configLoader */
                $configLoader = $this->getPackageLoader($package);

                $data->merge($configLoader->load($path));
            }
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
}
