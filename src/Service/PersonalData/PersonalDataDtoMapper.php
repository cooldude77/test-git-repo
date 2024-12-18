<?php

namespace App\Service\PersonalData;

use App\DTO\PersonalData\PersonalDataDto;
use App\Entity\PersonalData;
use App\Entity\User;

class PersonalDataDtoMapper
{

    public function mapToEntityForCreate(PersonalDataDto $personalDataDto, User $user): PersonalData
    {
        $entity = new PersonalData();

        $entity->setUser($user);
        $entity->setFirstName($personalDataDto->firstName);
        $entity->setMiddleName($personalDataDto->middleName);
        $entity->setLastName($personalDataDto->lastName);
        $entity->setAboutMe($personalDataDto->aboutMe);

        return $entity;

    }

    public function mapToEntityForUpdate(PersonalDataDto $personalDataDto, PersonalData $personalData): PersonalData
    {
        $personalData->setFirstName($personalDataDto->firstName);
        $personalData->setMiddleName($personalDataDto->middleName);
        $personalData->setLastName($personalDataDto->lastName);
        $personalData->setAboutMe($personalDataDto->aboutMe);

        return $personalData;

    }
}