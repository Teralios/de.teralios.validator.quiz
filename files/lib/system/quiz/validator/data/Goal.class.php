<?php

namespace wcf\system\quiz\validator\data;

// imports
use wcf\system\quiz\validator\Validator;

class Goal extends AbstractDataHolder
{
    const DATA_KEYS = [
        'title' => [true, Validator::TYPE_STRING, null],
        'icon' => [true, Validator::TYPE_STRING, null],
        'description' => [false, Validator::TYPE_STRING, null],
        'points' => [true, Validator::TYPE_INT, null]
    ];
}
