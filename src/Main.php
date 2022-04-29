<?php

namespace ROrier\Core;

use Exception;
use ROrier\Core\Components\Bootstrapper;
use ROrier\Core\Interfaces\AppInterface;
use ROrier\Core\Interfaces\MainInterface;

abstract class Main implements MainInterface
{
    static private ?AppInterface $app = null;

    /**
     * @inheritDoc
     * @throws Exception
     */
    static public function boot(): Bootstrapper
    {
        $config = [
            'main_class_name' => static::class
        ];

        $stack = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
        $trace = array_pop($stack);

        if (self::isValidTrace($trace)) {
            $config['root'] = dirname($trace['file']);
        }

        return new Bootstrapper($config);
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
     * @inheritDoc
     * @throws Exception
     */
    public static function app(): AppInterface
    {
        if (static::$app === null) {
            throw new Exception("App is undefined. Use boot() method to build application.");
        }

        return self::$app;
    }

    /**
     * @inheritDoc
     */
    static public function save(AppInterface $app): void
    {
        static::$app = $app;
    }
}
