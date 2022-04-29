<?php

namespace ROrier\Core\Features\Bootstrappers;

use Exception;
use ROrier\Container\Exceptions\ContainerException;
use ROrier\Container\Interfaces\ContainerInterface;
use ROrier\Core\Interfaces\AppInterface;
use ROrier\Core\Main;

trait AppBootstrapperTrait
{
    protected array $knownServices = [
        'container',
        'parameters',
        'library.services',
        'factory.services',
        'builder.workbench.services',
        'builder.service',
        'analyzer.config',
        'analyzer.argument',
        'compilator.spec.services'
    ];

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
        $this->saveKnownServices();

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
    protected function saveKnownServices(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getService('container');

        foreach ($this->knownServices as $knownService) {
            if (isset($this->boot[$knownService])) {
                $container->setService($knownService, $this->boot[$knownService]);
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
