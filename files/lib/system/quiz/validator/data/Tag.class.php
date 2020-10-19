<?php

namespace wcf\system\quiz\validator\data;

// imports
use wcf\system\quiz\validator\Validator;

class Tag extends AbstractDataHolder
{
    const DATA_KEYS = [
        'name' => [true, Validator::TYPE_STRING, null]
    ];
}
