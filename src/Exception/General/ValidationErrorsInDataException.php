<?php

namespace App\Exception\General;

use Exception;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationErrorsInDataException extends Exception
{

    /**
     * @param ConstraintViolationListInterface $errors
     * @param mixed $data
     */
    public function __construct(private readonly ConstraintViolationListInterface $errors, private readonly mixed $data)
    {
        parent::__construct("Validation Errors have occurred");
    }

    public function getErrors(): ConstraintViolationListInterface
    {
        return $this->errors;
    }

    public function getData(): mixed
    {
        return $this->data;
    }
}