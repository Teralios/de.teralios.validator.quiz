<?php

namespace wcf\system\quiz\validator\data;

abstract class AbstractDataHolder implements IDataHolder
{
    protected $data = [];

    public function setData(string $key, $value)
    {
        $this->data[$key] = $value;
    }

    public function getData(string $key)
    {
        return (isset($this->data[$key])) ? $this->data[$key] : null;
    }

    public function __get($name)
    {
        return $this->getData($name);
    }

    public static function getDataValues(): array
    {
        $reflector = new \ReflectionClass(static::class);
        if ($reflector->hasConstant('DATA_VALUES')) {
            return static::DATA_VALUES;
        }

        return [];
    }

    public static function getDataKeys(): array
    {
        $reflector = new \ReflectionClass(static::class);
        if ($reflector->hasConstant('DATA_KEYS')) {
            return static::DATA_KEYS;
        }

        return [];
    }
}
