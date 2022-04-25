<?php

namespace ROrier\Core\Interfaces;

use Exception;

interface KernelInterface
{
    /**
     * @param PackageInterface $package
     * @return self
     * @throws Exception
     */
    public function addPackage(PackageInterface $package): self;

    /**
     * @param string $name
     * @return bool
     */
    public function hasPackage(string $name): bool;

    /**
     * @param string $name
     * @return PackageInterface
     * @throws Exception
     */
    public function getPackage(string $name): ?PackageInterface;

    /**
     * @return PackageInterface[]
     */
    public function getPackages(): array;
}
