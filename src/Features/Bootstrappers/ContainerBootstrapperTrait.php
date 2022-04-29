<?php

namespace ROrier\Core\Features\Bootstrappers;

use ROrier\Config\Services\Analyzer;
use ROrier\Config\Services\ConfigParsers\ArrayParameterParser;
use ROrier\Config\Services\ConfigParsers\ConstantParser;
use ROrier\Config\Services\ConfigParsers\EnvParser;
use ROrier\Config\Services\ConfigParsers\StringParameterParser;
use ROrier\Container\Interfaces\ContainerInterface;
use ROrier\Container\Interfaces\ServiceBuilderInterface;
use ROrier\Container\Interfaces\ServiceFactoryInterface;
use ROrier\Container\Interfaces\ServiceWorkbenchBuilderInterface;
use ROrier\Container\Services\Builders\ServiceBuilder;
use ROrier\Container\Services\Builders\ServiceWorkbenchBuilder;
use ROrier\Container\Services\ConfigParsers\ServiceParser;
use ROrier\Container\Services\Container;
use ROrier\Container\Services\DelayedProxies\ContainerProxy;
use ROrier\Container\Services\Factories\ServiceFactory;
use ROrier\Container\Services\ServiceBuilderModules\CallsModule;
use ROrier\Container\Services\ServiceBuilderModules\CatchModule;
use ROrier\Container\Services\ServiceBuilderModules\ConfigModule;
use ROrier\Container\Services\ServiceBuilderModules\ConstructorModule;
use ROrier\Container\Services\ServiceBuilderModules\FactoryModule;

trait ContainerBootstrapperTrait
{
    /**
     * @return ContainerInterface
     */
    protected function buildContainer(): ContainerInterface
    {
        $container = new Container(
            $this->getService('library.services'),
            $this->getService('factory.services')
        );

        $this->getDelayedContainer()->setContainer($container);

        return $container;
    }

    protected function buildServiceFactory(): ServiceFactoryInterface
    {
        return new ServiceFactory(
            $this->getDelayedContainer(),
            $this->getService('library.services'),
            $this->getService('builder.workbench.services')
        );
    }

    protected function buildServiceWorkbenchBuilder(): ServiceWorkbenchBuilderInterface
    {
        return new ServiceWorkbenchBuilder(
            $this->getDelayedContainer(),
            $this->getService('analyzer.argument'),
            $this->getService('builder.service')
        );
    }

    protected function buildServiceBuilder(): ServiceBuilderInterface
    {
        return new ServiceBuilder([
            new ConstructorModule($this->getService('analyzer.argument')),
            new FactoryModule($this->getDelayedContainer(), $this->getService('analyzer.argument'))
        ],[
            new ConfigModule($this->getService('analyzer.config')),
            new CallsModule(),
            new CatchModule($this->getService('library.services'))
        ]);
    }

    protected function buildArgumentAnalyzer(): Analyzer
    {
        return new Analyzer([
            new ConstantParser(),
            new EnvParser(),
            new StringParameterParser($this->getService('parameters')),
            new ArrayParameterParser($this->getService('parameters')),
            new ServiceParser($this->getDelayedContainer())
        ]);
    }

    /**
     * @return ContainerProxy
     */
    protected function getDelayedContainer(): ContainerProxy
    {
        static $delayedContainer = null;

        if ($delayedContainer === null) {
            $delayedContainer = new ContainerProxy();
        }

        return $delayedContainer;
    }
}
