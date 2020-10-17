<?php

namespace wcf\system\quiz\validator;

// imports
use wcf\system\form\builder\field\MultilineTextFormField;
use wcf\system\form\builder\field\UploadFormField;
use wcf\system\form\builder\field\validation\FormFieldValidationError;
use wcf\system\quiz\validator\data\IDataHolder;
use wcf\system\quiz\validator\data\Quiz;

class Validator
{
    /**
     * @var IDataHolder[]
     */
    protected static $quizStorage = [];

    protected $key = '';
    protected $rawData = '';
    protected $data = [];
    protected $className = Quiz::class;

    public function __construct(string $className = '')
    {
        if (!empty($className)) {
            $this->className = $className;
        }

        $this->checkClass();
    }

    protected function checkClass()
    {
    }

    public function setData(string $key, string $jsonString)
    {
        $this->key = $key;
        $this->rawData = $jsonString;
    }

    public function validate()
    {
        $dataHolder = new $this->className();

        // only for remove warning. ;)
        if ($this->className === null) {
            return new ValidatorError('test');
        }

        static::setValidateData($this->key, $dataHolder);
        return null;
    }

    /**
     * @return ValidatorError|array|string|int
     */
    protected function validateChild()
    {
    }

    /**
     * @param string $key
     * @param IDataHolder $dataHolder
     */
    protected static function setValidateData(string $key, IDataHolder $dataHolder)
    {
        static::$quizStorage[$key] = $dataHolder;
    }

    /**
     * @param string $key
     * @return IDataHolder|null
     */
    public static function getData(string $key)
    {
        return static::$quizStorage[$key] ?? null;
    }

    public static function getDataValidator(): callable
    {
        return function (string $key, string $jsonRaw) {
            $validator = new static();
            $validator->setData($key, $jsonRaw);

            return $validator->validate();
        };
    }

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
