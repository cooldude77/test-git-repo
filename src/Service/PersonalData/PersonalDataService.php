<?php

namespace App\Service\PersonalData;

use App\DTO\PersonalData\PersonalDataDto;
use App\Entity\PersonalData;
use App\Entity\User;
use App\Exception\General\ValidationErrorsInDataException;
use App\Exception\PersonalData\PersonalDataAlreadyExistsAndCannotBeCreatedException;
use App\Exception\PersonalData\PersonalDataDoesNotExistsAndCannotBeCreatedHere;
use App\Repository\PersonalDataRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PersonalDataService
{
    public function __construct(private readonly PersonalDataDtoMapper  $personalDataDtoMapper,
                                private readonly PersonalDataRepository $personalDataRepository,
                                private readonly ValidatorInterface     $validator
    )
    {
    }

    /**
     * @throws PersonalDataAlreadyExistsAndCannotBeCreatedException|ValidationErrorsInDataException
     */
    public function create(PersonalDataDto $personalDataDto, User $user): PersonalData
    {

        $errors = $this->validator->validate($personalDataDto);
        if (count($errors) > 0)
            throw new ValidationErrorsInDataException($errors, $personalDataDto);

        $personalDataEntity = $this->personalDataRepository->findOneBy(['user' => $user]);
        if ($personalDataEntity != null)
            throw new PersonalDataAlreadyExistsAndCannotBeCreatedException($personalDataDto, $user);

        $personalDataEntity = $this->personalDataDtoMapper->mapToEntityForCreate($personalDataDto, $user);

        $this->personalDataRepository->persistAndFlush($personalDataEntity);

        return $personalDataEntity;
    }

    /**
     * @throws ValidationErrorsInDataException
     * @throws PersonalDataDoesNotExistsAndCannotBeCreatedHere
     */
    public function update(PersonalDataDto $personalDataDto, User $user): PersonalData
    {

        $errors = $this->validator->validate($personalDataDto);
        if (count($errors) > 0)
            throw new ValidationErrorsInDataException($errors, $personalDataDto);

        $personalDataEntity = $this->personalDataRepository->findOneBy(['user' => $user]);

        if ($personalDataEntity == null)
            throw new PersonalDataDoesNotExistsAndCannotBeCreatedHere();

        $personalDataEntity = $this->personalDataDtoMapper->mapToEntityForUpdate($personalDataDto, $personalDataEntity);

        $this->personalDataRepository->persistAndFlush($personalDataEntity);

        return $personalDataEntity;
    }
}