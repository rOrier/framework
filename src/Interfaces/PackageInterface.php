<?php

namespace ROrier\Core\Interfaces;

interface PackageInterface
{
    /**
     * @return string
     */
    public function getRoot(): string;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getConfigPath(): string;

    /**
     * @return array
     */
    public function buildParameters(): array;

    /**
     * @return array
     */
    public function buildServices(): array;
}
