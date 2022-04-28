<?php

namespace ROrier\Core\Features\Bootstrappers;

use Exception;
use ROrier\Core\Interfaces\AppInterface;

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
     * @return AppInterface
     * @throws Exception
     */
    public function buildLocalApp(?string $className = self::DEFAULT_LOCAL_APP): AppInterface
    {
        $this->saveKnownServices();

        return new $className(
            $this->getRoot(),
            $this->getKernel(),
            $this->getParameters(),
            $this->getContainer()
        );
    }

    /**
     * @param string|null $className
     * @return AppInterface
     * @throws Exception
     */
    public function buildGlobalApp(?string $className = self::DEFAULT_GLOBAL_APP): AppInterface
    {
        $this->saveKnownServices();

        $className::init(
            $this->getRoot(),
            $this->getKernel(),
            $this->getParameters(),
            $this->getContainer()
        );

        return $className::get();
    }

    protected function saveKnownServices(): void
    {
        $container = $this->getContainer();

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
