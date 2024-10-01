<?php

namespace ROrier\Core\Interfaces;

use ArrayAccess;

interface PackageInterface extends ArrayAccess
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
     * @return int
     */
    public function getPriority(): int;

    /**
     * @return string
     */
    public function getParametersConfigPath(): string;

    /**
     * @return string
     */
    public function getServicesConfigPath(): string;
}
