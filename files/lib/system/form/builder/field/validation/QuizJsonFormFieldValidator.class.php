<?php

namespace wcf\system\form\builder\field\validation;

// imports
use wcf\system\form\builder\field\MultilineTextFormField;
use wcf\system\form\builder\field\UploadFormField;
use wcf\system\quiz\validator\Validator;
use wcf\system\quiz\validator\ValidatorError;
use wcf\util\StringUtil;

/**
 * Class        QuizJsonFormFieldValidator
 * @package     QuizCreator
 * @subpackage  wcf\system\form\builder\field\validation
 * @author      Karsten (Teralios) Achterrath
 * @copyright   ©2020 Teralios.de
 * @license     GNU General Public License <https://www.gnu.org/licenses/gpl-3.0.txt>
 */
class QuizJsonFormFieldValidator extends FormFieldValidator
{

    /**
     * Returns FormFieldValidator to validate json quiz strings.
     * @param string $id
     * @param bool $upload
     * @return static
     */
    public static function getFormFieldValidator(string $id, bool $upload = true): self
    {
        return new static($id, ($upload) ? static::getUploadFunction() : static::getTextFunction());
    }


    /**
     * Creates validator object and sets data.
     * @param string $key
     * @param string $jsonString
     * @return ValidatorError|null
     */
    public static function validateData(string $key, string $jsonString): ?ValidatorError
    {
        $validator = new Validator();
        $validator->setData($key, $jsonString);
        $validator->validate();

        return $validator->validate();
    }

    /**
     * Contains function to check a UploadFormField.
     * @return callable
     */
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
            $jsonError = static::validateData($name, file_get_contents($file->getLocation()));

            if ($jsonError !== null) {
                $formField->addValidationError(
                    new FormFieldValidationError('json', 'wcf.acp.validator.quiz.data', ['details' => $jsonError])
                );
            }
        };
    }

    /**
     * Contains function to validate a TextFormField.
     * @return callable
     */
    protected static function getTextFunction(): callable
    {
        return function (MultilineTextFormField $formField) {
            $jsonString = $formField->getSaveValue();

            if (!empty($jsonString)) {
                // test json string
                $jsonError = static::validateData('text', $jsonString);

                if ($jsonError !== null) {
                    $formField->addValidationError(
                        new FormFieldValidationError('json', 'wcf.acp.validator.quiz.data', ['details' => $jsonError])
                    );
                }
            }
        };
    }
}
