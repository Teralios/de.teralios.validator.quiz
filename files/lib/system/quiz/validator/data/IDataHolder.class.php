<?php

namespace wcf\system\quiz\validator\data;

interface IDataHolder
{
    public function setData(string $key, $value);
    public function getData(string $key);
    public static function getDataKeys(): array;
}
