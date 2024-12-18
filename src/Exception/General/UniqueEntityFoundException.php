<?php

namespace App\Exception\General;

class UniqueEntityFoundException extends \Exception
{


    public function __construct(string $message = "")
    {
        parent::__construct($message . " already exists!!", 0);
    }


}