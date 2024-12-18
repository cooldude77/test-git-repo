<?php

namespace App\Exception\PersonalData;

class PersonalDataDoesNotExistsAndCannotBeCreatedHere extends \Exception
{
    public function __construct()
    {

        parent::__construct("The personal data does not exist. You will need to create it first . 
        If you are using API, use POST instead of PUT");
    }

}