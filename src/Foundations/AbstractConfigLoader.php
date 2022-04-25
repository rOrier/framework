<?php

namespace ROrier\Core\Foundations;

use ROrier\Config\Tools\CollectionTool;
use ROrier\Core\Interfaces\ConfigLoaderInterface;
use Symfony\Component\Finder\Finder;

abstract class AbstractConfigLoader implements ConfigLoaderInterface
{
    protected const FILE_PATTERN = null;

    public function load(string $path): array
    {
        $data = [];

        $filenames = $this->findFiles($path);

        foreach ($filenames as $filename) {
            CollectionTool::merge($data, $this->parseFile($filename));
        }

        return $data;
    }

    protected function findFiles($path)
    {
        $finder = new Finder();

        $finder
            ->in($path)
            ->files()
            ->name(static::FILE_PATTERN)
            ->sortByName()
        ;

        return array_keys(iterator_to_array($finder));
    }

    abstract protected function parseFile(string $path): array;
}
