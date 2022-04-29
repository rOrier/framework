<?php

namespace ROrier\Core\Features\Bootstrappers;

use Exception;
use ROrier\Container\Exceptions\ContainerException;
use ROrier\Container\Interfaces\ContainerInterface;
use ROrier\Container\Interfaces\ServiceLibraryInterface;
use ROrier\Core\Interfaces\AppInterface;

trait AppBootstrapperTrait
{
    /**
     * @throws ContainerException
     * @throws Exception
     */
    public function finalize(): void
    {
        $app = $this->getService('app');

        $this->saveFixedServices();
        $this->saveApp($app);
    }

    /**
     * @return AppInterface
     * @throws Exception
     */
    protected function buildApp(): AppInterface
    {
        $appClassName = $this->config['app_class_name'];

        return new $appClassName(
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
     * @throws Exception
     */
    protected function saveApp(AppInterface $app): void
    {
        $mainClassName = $this->config['main_class_name'];

        if (!in_array('ROrier\Core\Interfaces\MainInterface', class_implements($mainClassName))) {
            throw new Exception("Main class '$mainClassName' must implements 'ROrier\Core\Interfaces\MainInterface' interface.");
        }

        $mainClassName::save($app);
    }

    /**
     * @return string
     * @throws Exception
     */
    protected function getRoot(): string
    {
        if (!isset($this->config['root'])) {
            throw new Exception("Application root could not be automatically inferred. Use manual configuration to define required 'root' parameter.");
        }

        return $this->config['root'];
    }
}
