<?php

namespace ROrier\Core\Components;

use ROrier\Core\Features\Bootstrappers\AppBootstrapperTrait;
use ROrier\Core\Features\Bootstrappers\ContainerBootstrapperTrait;
use ROrier\Core\Features\Bootstrappers\KernelBootstrapperTrait;
use ROrier\Core\Features\Bootstrappers\LibraryBootstrapperTrait;
use ROrier\Core\Features\Bootstrappers\ParametersBootstrapperTrait;

class Bootstrapper
{
    use KernelBootstrapperTrait,
        ParametersBootstrapperTrait,
        LibraryBootstrapperTrait,
        ContainerBootstrapperTrait,
        AppBootstrapperTrait;

    protected const DEFAULT_LOCAL_APP = 'ROrier\Core\Components\LocalApp';
    protected const DEFAULT_GLOBAL_APP = 'ROrier\Core\Components\GlobalApp';

    protected Boot $boot;

    public function __construct(Boot $boot)
    {
        $this->boot = $boot;
    }
}
