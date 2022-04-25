<?php

namespace ROrier\Core\Components;

use Exception;
use ROrier\Core\Interfaces\KernelInterface;
use ROrier\Core\Interfaces\PackageInterface;

class Kernel implements KernelInterface
{
    /** @var PackageInterface[] */
    private array $packages = [];

    /**
     * @inheritDoc
     */
    public function addPackage(PackageInterface $package): self
    {
        if ($this->hasPackage($package->getName())) {
            throw new Exception("Package '{$package->getName()}' already registered.");
        }

        $this->packages[$package->getName()] = $package;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function hasPackage(string $name): bool
    {
        return isset($this->packages[$name]);
    }

    /**
     * @inheritDoc
     */
    public function getPackage(string $name): ?PackageInterface
    {
        if (!$this->hasPackage($name)) {
            throw new Exception("Package '$name' not found.");
        }

        return $this->packages[$name];
    }

    /**
     * @inheritDoc
     */
    public function getPackages(): array
    {
        return $this->packages;
    }
}
