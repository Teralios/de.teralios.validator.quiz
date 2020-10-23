<?php

namespace wcf\system\quiz\validator\data;

// imports
use wcf\system\quiz\validator\Validator;

/**
 * Class        Goal
 * @package     QuizCreator
 * @subpackage  wcf\system\quiz\validator\data
 * @author      Karsten (Teralios) Achterrath
 * @copyright   ©2020 Teralios.de
 * @license     GNU General Public License <https://www.gnu.org/licenses/gpl-3.0.txt>
 */
class Goal extends AbstractDataHolder
{
    // needed data for data holder.
    const DATA_KEYS = [
        'title' => [true, Validator::TYPE_STRING, null],
        'icon' => [true, Validator::TYPE_STRING, null],
        'description' => [false, Validator::TYPE_STRING, null],
        'points' => [true, Validator::TYPE_INT, null]
    ];
}
