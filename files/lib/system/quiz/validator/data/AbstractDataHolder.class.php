<?php

namespace wcf\system\quiz\validator\data;

/**
 * Class        AbstractDataHolder
 * @package     QuizCreator
 * @subpackage  wcf\system\quiz\validator\data
 * @author      Karsten (Teralios) Achterrath
 * @copyright   ©2020 Teralios.de
 * @license     GNU General Public License <https://www.gnu.org/licenses/gpl-3.0.txt>
 */
abstract class AbstractDataHolder implements IDataHolder
{
    /**
     * @var mixed[]
     */
    protected $data = [];

    /**
     * @inheritdoc
     */
    public function setData(string $key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @inheritdoc
     */
    public function has(string $key): bool
    {
        return (isset($this->data[$key]));
    }

    /**
     * __isset()
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return $this->has($name);
    }

    /**
     * __get()
     * @param $name
     * @return mixed|null
     */
    public function __get($name)
    {
        return (isset($this->data[$name])) ? $this->data[$name] : null;
    }

    /**
     * @inheritdoc
     */
    public static function getDataKeys(): array
    {
        $reflector = new \ReflectionClass(static::class);
        if ($reflector->hasConstant('DATA_KEYS')) {
            return static::DATA_KEYS;
        }

        return [];
    }
}
