<?php

namespace ROrier\Core\Features\Bootstrappers;

use Exception;
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
            $this->getLibrary(),
            $this->getServiceFactory()
        );

        $this->getDelayedContainer()->setContainer($container);

        $this->boot['container'] = $container;

        return $container;
    }

    protected function getServiceFactory(): ServiceFactoryInterface
    {
        if (!isset($this->boot['factory.services'])) {
            $this->boot['factory.services'] = new ServiceFactory(
                $this->getDelayedContainer(),
                $this->getLibrary(),
                $this->getServiceWorkbenchBuilder()
            );
        }

        return $this->boot['factory.services'];
    }

    protected function getServiceWorkbenchBuilder(): ServiceWorkbenchBuilderInterface
    {
        if (!isset($this->boot['builder.workbench.services'])) {
            $this->boot['builder.workbench.services'] = new ServiceWorkbenchBuilder(
                $this->getDelayedContainer(),
                $this->getArgumentAnalyzer(),
                $this->getServiceBuilder()
            );
        }

        return $this->boot['builder.workbench.services'];
    }

    protected function getServiceBuilder(): ServiceBuilderInterface
    {
        if (!isset($this->boot['builder.service'])) {
            $this->boot['builder.service'] = new ServiceBuilder([
                new ConstructorModule($this->getArgumentAnalyzer()),
                new FactoryModule($this->getDelayedContainer(), $this->getArgumentAnalyzer())
            ],[
                new ConfigModule($this->getConfigAnalyzer()),
                new CallsModule(),
                new CatchModule($this->getLibrary())
            ]);
        }

        return $this->boot['builder.service'];
    }

    protected function getArgumentAnalyzer(): Analyzer
    {
        if (!isset($this->boot['analyzer.argument'])) {
            $this->boot['analyzer.argument'] = new Analyzer([
                new ConstantParser(),
                new EnvParser(),
                new StringParameterParser($this->getParameters()),
                new ArrayParameterParser($this->getParameters()),
                new ServiceParser($this->getDelayedContainer())
            ]);
        }

        return $this->boot['analyzer.argument'];
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

    /**
     * @return ContainerInterface
     * @throws Exception
     */
    protected function getContainer(): ContainerInterface
    {
        static $container = null;

        if ($container === null) {
            $container = $this->buildContainer();
        }

        return $container;
    }
}
