<?php

namespace wcf\system\quiz\validator\data;

interface IRawData
{
    public function setRawData(string $rawData);
    public function getRawData(): string;
}
