<?php

namespace ROrier\Core;

use ROrier\Core\Components\Boot;
use ROrier\Core\Components\Bootstrappers\AppBootstrapper;
use ROrier\Core\Components\Bootstrappers\KernelBootstrapper;

abstract class Main
{
    static public function boot(): KernelBootstrapper
    {
        $boot = new Boot();

        $stack = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
        $trace = array_pop($stack);

        if (self::isValidTrace($trace)) {
            $boot['root'] = dirname($trace['file']);
        }

        return new KernelBootstrapper($boot);
    }

    /**
     * @param mixed $trace
     * @return bool
     */
    static protected function isValidTrace($trace)
    {
        return is_array($trace) && array_key_exists('file', $trace) && !empty($trace['file']);
    }
}
