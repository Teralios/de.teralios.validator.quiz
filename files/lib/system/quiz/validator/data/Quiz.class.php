<?php

namespace wcf\system\quiz\validator\data;

// imports
use wcf\system\quiz\validator\Validator;

class Quiz extends AbstractDataHolder implements IRawData
{
    const DATA_KEYS = [
        'title' => [true, Validator::TYPE_STRING, null],
        'type' => [true, Validator::TYPE_STRING, ['fun', 'competition']],
        'languageCode' => [false, Validator::TYPE_STRING, 2],
        'description' => [true, Validator::TYPE_STRING, null],
        'questions' => [true, Validator::TYPE_ARRAY, Question::class],
        'goals' => [false, Validator::TYPE_ARRAY, Goal::class],
        'tags' => [false, Validator::TYPE_ARRAY, Tag::class]
    ];

    protected $rawData;

    public function setRawData(string $rawData)
    {
        $this->rawData = $rawData;
    }

    public function getRawData(): string
    {
        return $this->rawData;
    }
}
