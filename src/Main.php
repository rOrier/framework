<?php

namespace ROrier\Core;

use Exception;
use ROrier\Core\Components\Boot;
use ROrier\Core\Components\Bootstrapper;
use ROrier\Core\Interfaces\AppInterface;

abstract class Main
{
    static private ?AppInterface $app = null;

    /**
     * @return Bootstrapper
     * @throws Exception
     */
    static public function boot(): Bootstrapper
    {
        $boot = new Boot();

        $stack = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
        $trace = array_pop($stack);

        if (self::isValidTrace($trace)) {
            $boot['root'] = dirname($trace['file']);
        }

        return new Bootstrapper($boot);
    }

    /**
     * @param mixed $trace
     * @return bool
     */
    static protected function isValidTrace($trace)
    {
        return is_array($trace) && array_key_exists('file', $trace) && !empty($trace['file']);
    }

    /**
     * @return AppInterface
     * @throws Exception
     */
    public static function app(): AppInterface
    {
        if (static::$app === null) {
            throw new Exception("App is undefined. Use boot() method to build application.");
        }

        return self::$app;
    }

    static public function save(AppInterface $app)
    {
        static::$app = $app;
    }
}
