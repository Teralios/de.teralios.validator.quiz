<?php

namespace wcf\system\validator\data\quiz;

// imports
use wcf\system\validator\data\AbstractDataHolder;
use wcf\system\validator\data\IRawData;
use wcf\system\validator\Validator;

/**
 * Class        Quiz
 * @package     QuizCreator
 * @subpackage  wcf\system\quiz\validator\data
 * @author      Karsten (Teralios) Achterrath
 * @copyright   ©2020 Teralios.de
 * @license     GNU General Public License <https://www.gnu.org/licenses/gpl-3.0.txt>
 *
 * @property-read string $title
 * @property-read string $languageCode
 * @property-read string $description
 * @property-read Question[] $questions
 * @property-read Goal[] $goals
 * @property-read Tag[] $tags
 */
class Quiz extends AbstractDataHolder implements IRawData
{
    // needed data for data holder.
    const DATA_KEYS = [
        'title' => [true, Validator::TYPE_STRING, null],
        'languageCode' => [false, Validator::TYPE_STRING, 2],
        'description' => [true, Validator::TYPE_STRING, null],
        'questions' => [true, Validator::TYPE_ARRAY, Question::class],
        'goals' => [false, Validator::TYPE_ARRAY, Goal::class],
        'tags' => [false, Validator::TYPE_ARRAY, Tag::class],
    ];

    /**
     * @var string
     */
    protected $rawData;

    /**
     * @inheritdoc
     */
    public function setRawData(string $rawData): self
    {
        $this->rawData = $rawData;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getRawData(): string
    {
        return $this->rawData;
    }
}
