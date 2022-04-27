<?php

namespace ROrier\Core\Components\Bootstrappers;

use Exception;
use ROrier\Core\Components\App;
use ROrier\Core\Foundations\AbstractBootstrapper;

class AppBootstrapper extends AbstractBootstrapper
{
    private string $root;

    public function createApp(string $root): KernelBootstrapper
    {
        if (!is_dir($root)) {
            throw new Exception("Root folder not found : $root");
        }

        $this->boot['app'] = $this->buildApp($root);

        return new KernelBootstrapper($this->boot);
    }

    protected function buildApp(string $root)
    {
        return new App($root);
    }
}
