<?php

namespace ROrier\Core\Components\ConfigLoaders;

use ROrier\Core\Foundations\AbstractConfigLoader;

class JsonLoader extends AbstractConfigLoader
{
    protected const EXT = 'json';

    protected function parseFile(string $path): array
    {
        return json_decode(file_get_contents($path), true);
    }
}
