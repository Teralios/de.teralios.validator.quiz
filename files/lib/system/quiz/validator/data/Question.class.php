<?php

namespace wcf\system\quiz\validator\data;

// imports
use wcf\system\quiz\validator\Validator;

class Question extends AbstractDataHolder
{
    const DATA_KEYS = [
        'question' => [true, Validator::TYPE_STRING, null],
        'optionA' => [true, Validator::TYPE_STRING, null],
        'optionB' => [true, Validator::TYPE_STRING, null],
        'optionC' => [true, Validator::TYPE_STRING, null],
        'optionD' => [true, Validator::TYPE_STRING, null],
        'answer' => [true, Validator::TYPE_STRING, null],
        'explanation' => [false, Validator::TYPE_STRING, null],
        'position' => [true, Validator::TYPE_INT, null]
    ];
}
