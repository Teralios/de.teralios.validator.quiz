<?php

namespace wcf\system\quiz\validator;

class ValidatorError
{
    const ERROR_REQUIRED = 1;
    const ERROR_TYPE = 2;
    protected $type;
    protected $key;
    protected $index;
    protected $parent = null;

    public function __construct(string $key, int $index = 0, int $type = ValidatorError::ERROR_REQUIRED)
    {
        $this->key = $key;
        $this->index = $index;
        $this->type = $type;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function setParent(ValidatorError $parent)
    {
        $this->parent = $parent;
    }

    public function getParent()
    {
        return $this->parent;
    }
}
