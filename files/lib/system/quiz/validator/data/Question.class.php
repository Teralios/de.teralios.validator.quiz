<?php

namespace wcf\system\quiz\validator\data;

class Question extends AbstractDataHolder
{
    const DATA_KEYS = [
        'question' => true,
        'optionA' => true,
        'optionB' => true,
        'optionC' => true,
        'optionD' => true,
        'answer' => true,
        'explanation' => false,
        'position' => true
    ];

    const DATA_VALUES = [
        'question' => ['string'],
        'optionA' => ['string'],
        'optionB' => ['string'],
        'optionC' => ['string'],
        'optionD' => ['string'],
        'answer' => ['string', ['A', 'B', 'C', 'D']],
        'explanation' => ['string'],
        'position' => ['int']
    ];
}
