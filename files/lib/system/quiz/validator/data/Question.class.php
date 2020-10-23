<?php

namespace wcf\system\quiz\validator\data;

// imports
use wcf\system\quiz\validator\Validator;

/**
 * Class        Question
 * @package     QuizCreator
 * @subpackage  wcf\system\quiz\validator\data
 * @author      Karsten (Teralios) Achterrath
 * @copyright   ©2020 Teralios.de
 * @license     GNU General Public License <https://www.gnu.org/licenses/gpl-3.0.txt>
 *
 * @property-read string $question
 * @property-read string $optionA
 * @property-read string $optionB
 * @property-read string $optionC
 * @property-read string $optionD
 * @property-read string $answer
 * @property-read string $explanation
 * @property-read int $position
 */
class Question extends AbstractDataHolder
{
    // needed data for data holder.
    const DATA_KEYS = [
        'question' => [true, Validator::TYPE_STRING, null],
        'optionA' => [true, Validator::TYPE_STRING, null],
        'optionB' => [true, Validator::TYPE_STRING, null],
        'optionC' => [true, Validator::TYPE_STRING, null],
        'optionD' => [true, Validator::TYPE_STRING, null],
        'answer' => [true, Validator::TYPE_STRING, ['A', 'B', 'C', 'D']],
        'explanation' => [false, Validator::TYPE_STRING, null],
        'position' => [true, Validator::TYPE_INT, null]
    ];
}
