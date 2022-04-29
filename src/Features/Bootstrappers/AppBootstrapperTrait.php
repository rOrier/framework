<?php

namespace ROrier\Core\Features\Bootstrappers;

use Exception;
use ROrier\Container\Exceptions\ContainerException;
use ROrier\Container\Interfaces\ContainerInterface;
use ROrier\Container\Interfaces\ServiceLibraryInterface;
use ROrier\Core\Interfaces\AppInterface;
use ROrier\Core\Main;

trait AppBootstrapperTrait
{
    /**
     * @param string $root
     * @return self
     * @throws Exception
     */
    public function overrideRoot(string $root): self
    {
        if (!is_dir($root)) {
            throw new Exception("Root folder not found : $root");
        }

        $this->boot['root'] = $root;

        return $this;
    }

    /**
     * @param string|null $className
     * @throws Exception
     */
    public function finalize(?string $className = self::APP_CLASS_NAME): void
    {
        $this->saveFixedServices();

        $app = $this->buildApp($className);

        Main::save($app);
    }

    /**
     * @param string $className
     * @return AppInterface
     * @throws Exception
     */
    protected function buildApp(string $className): AppInterface
    {
        return new $className(
            $this->getRoot(),
            $this->getService('kernel'),
            $this->getService('parameters'),
            $this->getService('container')
        );
    }

    /**
     * @throws ContainerException
     */
    protected function saveFixedServices(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getService('container');

        /** @var ServiceLibraryInterface $library */
        $library = $this->getService('library.services');

        foreach ($library->getFixedServices() as $fixedService) {
            if ($this->hasService($fixedService)) {
                $container->setService($fixedService, $this->getService($fixedService));
            }
        }
    }

    /**
     * @return string
     * @throws Exception
     */
    protected function getRoot(): string
    {
        if (!isset($this->boot['root'])) {
            throw new Exception("Application root could not be automatically inferred. Use overrideRoot() to set it.");
        }

        return $this->boot['root'];
    }
}
