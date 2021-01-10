<?php

namespace wcf\system\validator\data\quiz;

// imports
use wcf\system\validator\data\AbstractDataHolder;
use wcf\system\validator\Validator;

/**
 * Class        Tag
 * @package     QuizCreator
 * @subpackage  wcf\system\quiz\validator\data
 * @author      Karsten (Teralios) Achterrath
 * @copyright   ©2020 Teralios.de
 * @license     GNU General Public License <https://www.gnu.org/licenses/gpl-3.0.txt>
 */
class Tag extends AbstractDataHolder
{
    // needed data for data holder.
    const DATA_KEYS = [
        'name' => [true, Validator::TYPE_STRING, null]
    ];
}
