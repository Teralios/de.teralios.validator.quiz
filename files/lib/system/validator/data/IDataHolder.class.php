<?php

namespace wcf\system\validator\data;

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
     * @return self
     */
    public function setData(string $key, $value): self;

    /**
     * Returns data array.
     * @return mixed[]
     */
    public function getData(): array;

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
