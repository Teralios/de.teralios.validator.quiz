<?php

namespace wcf\system\form\builder\field\validation;

// imports
use wcf\system\form\builder\field\MultilineTextFormField;
use wcf\system\form\builder\field\UploadFormField;
use wcf\system\quiz\validator\Validator;
use wcf\system\quiz\validator\ValidatorError;
use wcf\util\StringUtil;

class QuizJsonFormFieldValidator extends FormFieldValidator
{

    public static function getFormFieldValidator(string $id, bool $upload = true)
    {
        return new static($id, ($upload) ? static::getUploadFunction() : static::getTextFunction());
    }


    public static function getDataValidator(string $key, string $jsonString): Validator
    {
        $validator = new Validator();
        $validator->setData($key, $jsonString);

        return $validator;
    }

    protected static function getUploadFunction(): callable
    {
        return function (UploadFormField $formField) {
            $file = $formField->getValue()[0];
            $name = $file->getFilename();

            // file extension
            if (!StringUtil::endsWith($name, '.quiz') && !StringUtil::endsWith($name, '.json')) {
                $formField->addValidationError(new FormFieldValidationError('fileExtension', 'wcf.acp.validator.quiz.file.extension'));
                return;
            }

            // json test
            /** @var ValidatorError $jsonError */
            $jsonError = static::getDataValidator($name, file_get_contents($file->getLocation()));

            if ($jsonError !== null) {
                $formField->addValidationError(
                    new FormFieldValidationError('json', 'wcf.acp.validator.quiz.data', ['details' => $jsonError])
                );
            }
        };
    }

    protected static function getTextFunction(): callable
    {
        return function (MultilineTextFormField $formField) {
            $jsonString = $formField->getSaveValue();

            if (!empty($jsonString)) {
                // test json string
                $jsonError = static::getDataValidator('text', $jsonString);

                if ($jsonError !== null) {
                    $formField->addValidationError(
                        new FormFieldValidationError('json', 'wcf.acp.validator.quiz.data', ['details' => $jsonError])
                    );
                }
            }
        };
    }
}
