<?php

namespace ROrier\Core\Foundations;

use ROrier\Core\Components\Boot;

class AbstractBootstrapper
{
    protected Boot $boot;

    public function __construct(Boot $boot)
    {
        $this->boot = $boot;
    }
}
