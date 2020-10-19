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
     */
    public function setData(string $key, string $jsonString)
    {
        $this->key = $key;
        $this->rawData = $jsonString;
    }

    /**
     * Validate data.
     * @return ValidatorError|null
     */
    public function validate()
    {
        // json data
        try {
            $data = ArrayUtil::trim(JSON::decode($this->rawData));
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
        $index = 0;
        foreach ($dataKeys as $key => $settings) {
            list($isRequired, $type, $dataOptions) = $settings;

            // data exists and is required?
            $dataExists = $this->dataExists();
            if (!$this->dataExists($key, $data)) {
                if ($isRequired) {
                    return new ValidatorError($key, $index);
                }

                continue;
            }

            // check type.
            $rawValue = $data[$key];
            if (!$this->checkType($rawValue, $type)) {
                return new ValidatorError($key, $index, ValidatorError::ERROR_TYPE);
            }

            // check data
            $value = $this->checkValue($key, $index, $type, $rawValue, $dataOptions);
            if ($value instanceof ValidatorError) {
                return $value;
            }

            // set validate data to data holder.
            $dataHolder->setData($key, $value);
            $index++;
        }

        return $dataHolder;
    }

    /**
     * Data exists and is not empty.
     * @param $data
     * @param string $key
     * @return bool
     */
    protected function dataExists($data, string $key)
    {
        return (isset($data[$key]) || empty($data[$key]));
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
            case static::TYPE_INT:
                return is_integer($value);
            case static::TYPE_ARRAY:
                return is_array($value);
            default:
                return true;
        }
    }

    /**
     * Checks data value.
     * @param string $key
     * @param int $index
     * @param int $type
     * @param $value
     * @param $dataOptions
     * @return int|string|IDataHolder|ValidatorError
     */
    protected function checkValue(string $key, int $index, int $type, $value, $dataOptions)
    {
        switch ($type) {
            case static::TYPE_ARRAY:
                $value = $this->validateData($value, $dataOptions);
                if ($value instanceof ValidatorError) {
                    $value->setParent(new ValidatorError($key, $index, ValidatorError::ERROR_INVALID));
                }
                return $value;
            case static::TYPE_STRING:
                return $this->checkString($key, $index, $value, $dataOptions);
            case static::TYPE_INT:
            default:
                return (int) $value;
        }
    }

    /**
     * Checks string.
     * @param string $key
     * @param int $index
     * @param string $value
     * @param $neededStrings
     * @return string|ValidatorError
     */
    protected function checkString(string $key, int $index, string $value, $neededStrings)
    {
        if (is_array($neededStrings) && !in_array($value, $neededStrings)) {
            return new ValidatorError($key, $index, Validator::TYPE_STRING);
        }

        return $value;
    }

    /**
     * Checks class.
     * @param $className
     * @throws ReflectionException
     */
    protected function checkClass($className)
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
    public static function getData(string $key)
    {
        return static::$quizStorage[$key] ?? null;
    }

    /**
     * Returns last data holder.
     * @return IDataHolder|null
     */
    public static function getLastValidatedData()
    {
        return static::getData(static::$lastKey);
    }

    /**
     * Returns validator for form field checks.
     * @return callable
     */
    public static function getDataValidator(): callable
    {
        return function (string $key, string $jsonRaw) {
            $validator = new static();
            $validator->setData($key, $jsonRaw);

            return $validator->validate();
        };
    }

    /**
     * Returns function for upload form field.
     * @return callable
     */
    public static function getUploadFieldValidator(): callable
    {
        return function (UploadFormField $formField) {
            $file = $formField->getValue()[0];
            $name = $file->getFilename();

            // file extension
            $lastDot = strrpos($name, '.');
            if ($lastDot !== false) {
                $extension = substr($name, $lastDot + 1);

                if ($extension !== 'json') {
                    $formField->addValidationError(new FormFieldValidationError('fileExtension', 'wcf.acp.quizCreator.import.error.file'));
                    return;
                }
            } else {
                $formField->addValidationError(new FormFieldValidationError('unknownExtension', 'wcf.acp.quizCreator.import.error.unknown'));
                return;
            }

            // json test
            /** @var ValidatorError $jsonError */
            $jsonError = static::getDataValidator()($name, file_get_contents($file->getLocation()));

            if ($jsonError !== null) {
                $formField->addValidationError(
                    new FormFieldValidationError('json', 'wcf.acp.quizCreator.import.error.json', ['details' => $jsonError])
                );
            }
        };
    }

    /**
     * Returns function for a text field.
     * @return callable
     */
    public static function getTextFieldValidator(): callable
    {
        return function (MultilineTextFormField $formField) {
            $jsonString = $formField->getSaveValue();

            if (!empty($jsonString)) {
                // test json string
                $jsonError = static::getDataValidator()('text', $jsonString);

                if ($jsonError !== null) {
                    $formField->addValidationError(
                        new FormFieldValidationError('json', 'wcf.acp.quizCreator.import.error.json', ['details' => $jsonError])
                    );
                }
            }
        };
    }
}
