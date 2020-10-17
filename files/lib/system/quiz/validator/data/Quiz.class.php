<?php

namespace wcf\system\quiz\validator\data;

class Quiz extends AbstractDataHolder implements IRawData
{
    const DATA_KEYS = [
        'title' => true,
        'type' => true,
        'description' => true,
        'questions' => true,
        'goals' => false,
        'tags' => false
    ];

    const DATA_VALUES = [
        'title' => ['string'],
        'type' => ['string', ['fun', 'competition']],
        'description' => ['string'],
        'questions' => ['array', Question::class],
        'goals' => ['array', Goal::class],
        'tags' => ['array', Tag::class]
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
