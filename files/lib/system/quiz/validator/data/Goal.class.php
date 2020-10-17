<?php

namespace wcf\system\quiz\validator\data;

class Goal extends AbstractDataHolder
{
    const DATA_KEYS = [
        'title' => true,
        'icon' => true,
        'description' => false,
        'points' => true
    ];

    const DATA_VALUES = [
        'title' => 'string',
        'icon' => 'string',
        'description' => 'string',
        'points' => 'int',
    ];
}
