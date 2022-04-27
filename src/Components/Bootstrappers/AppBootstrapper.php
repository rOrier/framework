<?php

namespace ROrier\Core\Components\Bootstrappers;

use Exception;
use ROrier\Core\Foundations\AbstractBootstrapper;

class AppBootstrapper extends AbstractBootstrapper
{
    public function setRoot(string $root): KernelBootstrapper
    {
        if (!is_dir($root)) {
            throw new Exception("Root folder not found : $root");
        }

        $this->boot['root'] = $root;

        return new KernelBootstrapper($this->boot);
    }
}
