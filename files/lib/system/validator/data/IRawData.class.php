<?php

namespace wcf\system\validator\data;

/**
 * Interface    IRawData
 * @package     QuizCreator
 * @subpackage  wcf\system\quiz\validator\data
 * @author      Karsten (Teralios) Achterrath
 * @copyright   ©2020 Teralios.de
 * @license     GNU General Public License <https://www.gnu.org/licenses/gpl-3.0.txt>
 */
interface IRawData
{
    /**
     * Sets raw json-string.
     * @param string $rawData
     */
    public function setRawData(string $rawData);

    /**
     * Returns raw json-string.
     * @return string
     */
    public function getRawData(): string;
}
