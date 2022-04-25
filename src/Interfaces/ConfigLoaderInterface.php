<?php

namespace ROrier\Core\Interfaces;

interface ConfigLoaderInterface
{
    public function load(string $path): array;
}
