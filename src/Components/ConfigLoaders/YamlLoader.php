<?php

namespace ROrier\Core\Components\ConfigLoaders;

use ROrier\Core\Foundations\AbstractConfigLoader;
use Symfony\Component\Yaml\Yaml;

class YamlLoader extends AbstractConfigLoader
{
    protected const FILE_PATTERN = '/.*\.(yml|yaml)/';

    protected function parseFile(string $path): array
    {
        return Yaml::parseFile($path, Yaml::PARSE_EXCEPTION_ON_INVALID_TYPE);
    }
}
