<?php

namespace wcf\system\validator;

/**
 * Class        ValidatorError
 * @package     QuizCreator
 * @subpackage  wcf\system\quiz\validator
 * @author      Karsten (Teralios) Achterrath
 * @copyright   ©2020 Teralios.de
 * @license     GNU General Public License <https://www.gnu.org/licenses/gpl-3.0.txt>
 */
class ValidatorError
{
    const ERROR_REQUIRED = 1;
    const ERROR_TYPE = 2;
    const ERROR_INVALID = 3;

    /**
     * @var int
     */
    protected $type;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var int
     */
    protected $index;

    /**
     * @var ValidatorError
     */
    protected $parent = null;

    /**
     * ValidatorError constructor.
     * @param string $key
     * @param int $type
     * @param int $index
     */
    public function __construct(string $key, int $type = ValidatorError::ERROR_REQUIRED, int $index = 0)
    {
        $this->key = $key;
        $this->type = $type;
        $this->index = $index;
    }

    /**
     * Returns key.
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Returns error type.
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * Returns index.
     * @return int
     */
    public function getIndex(): int
    {
        return $this->index;
    }

    /**
     * Set parent error.
     * @param ValidatorError $parent
     */
    public function setParent(ValidatorError $parent): void
    {
        $this->parent = $parent;
    }

    /**
     * Returns parent error.
     * @return ValidatorError|null
     */
    public function getParent(): ?ValidatorError
    {
        return $this->parent;
    }
}
