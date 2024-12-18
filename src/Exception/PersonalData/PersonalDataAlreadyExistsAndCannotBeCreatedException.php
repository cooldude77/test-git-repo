<?php

namespace App\Exception\PersonalData;

use App\DTO\PersonalData\PersonalDataDto;
use App\Entity\User;

class PersonalDataAlreadyExistsAndCannotBeCreatedException extends \Exception
{

    private PersonalDataDto $personalDataDto;
    private User $user;

    public function __construct(PersonalDataDto $personalDataDto, User $user)
    {
        parent::__construct();
        $this->personalDataDto = $personalDataDto;
        $this->user = $user;
    }
}