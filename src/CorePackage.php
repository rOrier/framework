<?php

namespace ROrier\Core;

use ROrier\Core\Foundations\AbstractPackage;

class CorePackage extends AbstractPackage
{
    protected const CONFIG_LOADER = 'ROrier\Core\Components\ConfigLoaders\JsonLoader';
}