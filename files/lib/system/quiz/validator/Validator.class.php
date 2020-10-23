<?php

namespace wcf\system\quiz\validator;

// imports
use ReflectionClass;
use ReflectionException;
use wcf\system\exception\ImplementationException;
use wcf\system\exception\SystemException;
use wcf\system\form\builder\field\MultilineTextFormField;
use wcf\system\form\builder\field\UploadFormField;
use wcf\system\form\builder\field\validation\FormFieldValidationError;
use wcf\system\quiz\validator\data\IDataHolder;
use wcf\system\quiz\validator\data\Quiz;
use wcf\util\ArrayUtil;
use wcf\util\JSON;
use wcf\util\StringUtil;

/**
 * Class        Validator
 * @package     QuizCreator
 * @subpackage  wcf\system\quiz\validator
 * @author      Karsten (Teralios) Achterrath
 * @copyright   ©2020 Teralios.de
 * @license     GNU General Public License <https://www.gnu.org/licenses/gpl-3.0.txt>
 */
class Validator
{
    const TYPE_STRING = 1;
    const TYPE_INT = 2;
    const TYPE_ARRAY = 3;

    /**
     * @var IDataHolder[]
     */
    protected static $quizStorage = [];

    /**
     * @var string
     */
    protected static $lastKey;

    /**
     * @var string
     */
    protected $key = '';

    /**
     * @var string
     */
    protected $rawData = '';

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var string
     */
    protected $className = Quiz::class;

    /**
     * @var string
     */
    protected $parentNode = '';

    /**
     * @var int
     */
    protected $parentIndex = 0;

    /**
     * Validator constructor.
     * @param string $className
     */
    public function __construct(string $className = '')
    {
        if (!empty($className)) {
            $this->className = $className;
        }
    }

    /**
     * Set data for validation.
     * @param string $key
     * @param string $jsonString
     * @return static
     */
    public function setData(string $key, string $jsonString): self
    {
        $this->key = $key;
        $this->rawData = $jsonString;

        return $this;
    }

    /**
     * Validate data.
     * @return ValidatorError|null
     */
    public function validate(): ?ValidatorError
    {
        // json data
        try {
            $data = ArrayUtil::trim(JSON::decode($this->rawData), false);
        } catch (SystemException $e) {
            return new ValidatorError('quiz');
        }
        $dataHolder = $this->validateData($data, $this->className);

        if ($dataHolder instanceof IDataHolder) {
            static::setValidateData($this->key, $dataHolder);
        }

        return ($dataHolder instanceof ValidatorError) ? $dataHolder : null;
    }

    /**
     * Validate data array.
     * @param array $data
     * @param string $className
     * @return ValidatorError|IDataHolder
     */
    protected function validateData(array $data, string $className)
    {
        // check class name.
        $this->checkClass($className);

        /** @var IDataHolder $dataHolder */
        $dataHolder = new $className();
        $dataKeys = $dataHolder->getDataKeys();

        // check data array
        foreach ($dataKeys as $key => $settings) {
            list($isRequired, $type, $dataOptions) = $settings;

            // data exists and is required?
            if (!$this->dataExists($key, $data)) {
                if ($isRequired) {
                    return new ValidatorError($key);
                }

                continue;
            }

            // check type.
            $rawValue = $data[$key];
            if (!$this->checkType($rawValue, $type)) {
                return new ValidatorError($key, ValidatorError::ERROR_TYPE);
            }

            // check data
            $value = $this->checkValue($key, $type, $rawValue, $dataOptions);
            if ($value instanceof ValidatorError) {
                return $value;
            }

            // set validate data to data holder.
            $dataHolder->setData($key, $value);
        }

        return $dataHolder;
    }

    /**
     * Data exists and is not empty.
     * @param $data
     * @param string $key
     * @return bool
     */
    protected function dataExists(string $key, array $data): bool
    {
        return (isset($data[$key]) || (!empty($data[$key]) && $data[$key] !== 0));
    }

    /**
     * Checks data type..
     * @param $value
     * @param int $type
     * @return bool
     */
    protected function checkType($value, int $type): bool
    {
        switch ($type) {
            case static::TYPE_STRING:
                return is_string($value);
            case static::TYPE_ARRAY:
                return is_array($value);
            default:
                return true;
        }
    }

    /**
     * Checks data value.
     * @param string $key
     * @param int $type
     * @param $value
     * @param $dataOptions
     * @return int|string|IDataHolder|ValidatorError
     */
    protected function checkValue(string $key, int $type, $value, $dataOptions)
    {
        switch ($type) {
            case static::TYPE_ARRAY:
                $this->parentNode = $key;
                $value = $this->checkArray($value, $dataOptions);
                if ($value instanceof ValidatorError) {
                    $value->setParent(new ValidatorError($key, ValidatorError::ERROR_INVALID, $this->parentIndex));
                }
                return $value;

            case static::TYPE_STRING:
                return $this->checkString($key, $value, $dataOptions);

            case static::TYPE_INT:
            default:
                return (int) $value;
        }
    }

    /**
     * Checks a data array with IDataHolder class.
     * @param array $values
     * @param string $dataOptions
     * @return array|IDataHolder|ValidatorError
     */
    protected function checkArray(array $values, string $dataOptions)
    {
        $returnValues = [];
        $index = 1;
        foreach ($values as $value) {
            $value = $this->validateData($value, $dataOptions);

            if ($value instanceof ValidatorError) {
                $this->parentIndex = $index;
                return $value;
            }

            $returnValues[] = $value;
            $index++;
        }

        return $returnValues;
    }

    /**
     * Checks string.
     * @param string $key
     * @param string $value
     * @param mixed $neededStrings
     * @return string|ValidatorError
     */
    protected function checkString(string $key, string $value, $neededStrings)
    {
        if (is_array($neededStrings) && !in_array($value, $neededStrings)) {
            return new ValidatorError($key, Validator::TYPE_STRING);
        }

        return $value;
    }

    /**
     * Checks class.
     * @param string $className
     * @throws ReflectionException
     */
    protected function checkClass(string $className)
    {
        $reflection = new ReflectionClass($className);

        if (!$reflection->implementsInterface(IDataHolder::class)) {
            throw new ImplementationException($className, IDataHolder::class);
        }
    }

    /**
     * Set the data holder a runtime cache.
     * @param string $key
     * @param IDataHolder $dataHolder
     */
    protected static function setValidateData(string $key, IDataHolder $dataHolder)
    {
        static::$quizStorage[$key] = $dataHolder;
        static::$lastKey = $key;
    }

    /**
     * Returns needed data holder.
     * @param string $key
     * @return IDataHolder|null
     */
    public static function getData(string $key): ?IDataHolder
    {
        return static::$quizStorage[$key] ?? null;
    }

    /**
     * Returns last data holder.
     * @return IDataHolder|null
     */
    public static function getLastValidatedData(): ?IDataHolder
    {
        return static::getData(static::$lastKey);
    }
}
