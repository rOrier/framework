<?php

namespace ROrier\Core\Interfaces;

use ROrier\Core\Components\Bootstrapper;

interface MainInterface
{
    /**
     * @param array $runtimeConfiguration
     * @return Bootstrapper
     */
    static public function boot(array $runtimeConfiguration = []): Bootstrapper;

    /**
     * @return AppInterface
     */
    public static function app(): AppInterface;

    /**
     * @param AppInterface $app
     */
    static public function save(AppInterface $app): void;
}
