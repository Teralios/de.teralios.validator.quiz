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
     * @param string $key
     * @param string $jsonString
     */
    public function setData(string $key, string $jsonString)
    {
        $this->key = $key;
        $this->rawData = $jsonString;
    }

    /**
     * @return ValidatorError|null
     */
    public function validate()
    {
        $dataHolder = new $this->className();

        // only for remove warning. ;)
        if ($this->className === null) {
            return new ValidatorError('test');
        }

        $dataHolder = $this->validateDate($this->key, $dataHolder);

        if ($dataHolder instanceof IDataHolder) {
            static::setValidateData($this->key, $dataHolder);
        }

        return ($dataHolder instanceof ValidatorError) ? $dataHolder : null;
    }

    /**
     * @return ValidatorError|IDataHolder
     */
    protected function validateData(array $data, string $className, int $depth = 0)
    {
        $this->checkClass($className);
        /** @var IDataHolder $dataHolder */
        $dataHolder = new $className();
        $dataKeys = $dataHolder->getDataKeys();

        foreach ($dataKeys as $key => $settings) {
            list($isRequired, $type, $validData) = $settings;

            if ($isRequired && (!isset($data[$key]) || empty($data[$key]))) {
                return new ValidatorError($key, $depth);
            }

            $rawValue = $data[$key];
            if (!$this->checkType($rawValue, $type)) {
                return new ValidatorError($key, $depth, ValidatorError::ERROR_TYPE);
            }

            if ($type == static::TYPE_ARRAY) {
                if (empty($validData)) {
                    throw new SystemException('Check needs a IDataHolder class.');
                }

                $value = $this->validateData($rawValue, $validData, $depth + 1);
            } elseif ($type == static::TYPE_STRING && is_array($validData)) {
                $value = (!in_array($rawValue, $validData)) ? new ValidatorError($key, $depth, ValidatorError::ERROR_INVALID) : $rawValue;
            }

            if ($value instanceof ValidatorError) {
                return $value;
            }

            $dataHolder->setData($key, $value);
        }

        return $dataHolder;
    }

    protected function checkType($value, int $type): bool
    {
        if ($type == static::TYPE_STRING && !is_string($value)) {
            return false;
        }

        if ($type == static::TYPE_INT && !is_integer($value)) {
            return false;
        }

        if ($type == static::TYPE_ARRAY && !is_array($value)) {
            return false;
        }

        return true;
    }

    /**
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
     * @param string $key
     * @param IDataHolder $dataHolder
     */
    protected static function setValidateData(string $key, IDataHolder $dataHolder)
    {
        static::$quizStorage[$key] = $dataHolder;
        static::$lastKey = $key;
    }

    /**
     * @param string $key
     * @return IDataHolder|null
     */
    public static function getData(string $key)
    {
        return static::$quizStorage[$key] ?? null;
    }

    /**
     * @return IDataHolder|null
     */
    public static function getLastValidatedData()
    {
        return static::getData(static::$lastKey);
    }

    /**
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
