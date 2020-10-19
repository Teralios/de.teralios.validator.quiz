<?php

namespace wcf\system\quiz\validator\data;

/**
 * Interface        IDataHolder
 * @package     QuizCreator
 * @subpackage  wcf\system\quiz\validator\data
 * @author      Karsten (Teralios) Achterrath
 * @copyright   ©2020 Teralios.de
 * @license     GNU General Public License <https://www.gnu.org/licenses/gpl-3.0.txt>
 */
interface IDataHolder
{
    /**
     * Sets a variable to data array.
     * @param string $key
     * @param string|int|IDataHolder $value
     * @return void
     */
    public function setData(string $key, $value);

    /**
     * Returns data array.
     * @return mixed[]
     */
    public function getData();

    /**
     * Returns true if data array has needed $key
     * @param $key
     * @return bool
     */
    public function has(string $key): bool;

    /**
     * Returns data structure.
     * @return array
     */
    public static function getDataKeys(): array;
}
