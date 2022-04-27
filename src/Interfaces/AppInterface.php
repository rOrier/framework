<?php

namespace ROrier\Core\Interfaces;

use Exception;

interface AppInterface
{
    /**
     * @return string
     */
    public function getRoot(): string;

    /**
     * @return KernelInterface
     * @throws Exception
     */
    public function getKernel(): KernelInterface;

    /**
     * @param string $address
     * @return mixed
     * @throws Exception
     */
    public function getParameter(string $address);

    /**
     * @param string $id
     * @return object
     * @throws Exception
     */
    public function getService(string $id): object;
}
