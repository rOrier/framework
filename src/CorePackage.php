<?php

namespace ROrier\Core;

use ROrier\Core\Foundations\AbstractPackage;

class CorePackage extends AbstractPackage
{
    protected const CUSTOM_CONFIGURATION = [
        'priority' => self::PRIORITY_FIRST,
    ];
}