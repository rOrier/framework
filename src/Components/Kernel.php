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

        $this->packages[] = $package;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function hasPackage(string $name): bool
    {
        foreach ($this->packages as $package) {
            if ($package->getName() === $name) {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function getPackage(string $name): ?PackageInterface
    {
        foreach ($this->packages as $package) {
            if ($package->getName() === $name) {
                return $package;
            }
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function getPackages(): array
    {
        return $this->packages;
    }
}
