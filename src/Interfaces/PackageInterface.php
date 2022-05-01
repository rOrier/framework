<?php

namespace ROrier\Core\Interfaces;

interface PackageInterface
{
    public const CONFIG_LOADER = 'ROrier\Core\Components\ConfigLoaders\YamlLoader';

    public const PATH_PARAMETERS = '/../config/parameters';
    public const PATH_SERVICES = '/../config/services';

    /**
     * @return string
     */
    public function getRoot(): string;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getParametersConfigPath(): string;

    /**
     * @return string
     */
    public function getServicesConfigPath(): string;
}
