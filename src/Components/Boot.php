<?php

namespace ROrier\Core\Components;

use Exception;
use ArrayAccess;

class Boot implements ArrayAccess
{
    private array $services = [];

    /**
     * @param mixed $offset
     * @param mixed $value
     * @throws Exception
     */
    public function offsetSet($offset, $value)
    {
        $this->services[$offset] = $value;
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->services[$offset]);
    }

    /**
     * @param mixed $offset
     * @throws Exception
     */
    public function offsetUnset($offset)
    {
        unset($this->services[$offset]);
    }

    /**
     * @param mixed $offset
     * @return array|bool|mixed|null
     */
    public function offsetGet($offset)
    {
        return $this->services[$offset];
    }
}
