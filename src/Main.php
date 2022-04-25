<?php

namespace ROrier\Core;

use ROrier\Core\Components\Boot;
use ROrier\Core\Components\Bootstrappers\AppBootstrapper;

abstract class Main
{
    static public function boot(): AppBootstrapper
    {
        return new AppBootstrapper(new Boot());
    }
}
