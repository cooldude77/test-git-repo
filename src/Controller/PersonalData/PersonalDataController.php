<?php

namespace App\Controller\PersonalData;

use App\DTO\PersonalData\PersonalDataDto;
use App\Entity\User;
use App\Exception\General\ValidationErrorsInDataException;
use App\Exception\PersonalData\PersonalDataAlreadyExistsAndCannotBeCreatedException;
use App\Exception\PersonalData\PersonalDataDoesNotExistsAndCannotBeCreatedHere;
use App\Service\PersonalData\PersonalDataService;
use App\Service\Response\JsonResponseProvider;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/v1', name: 'api_v1_personal_data_')]
class PersonalDataController extends AbstractController
{
    public function __construct(private readonly JsonResponseProvider $jsonResponseProvider,
                                private readonly SerializerInterface  $serializer)
    {
    }

    #[Route('/personal_data', name: 'personal_data_create', methods: ["POST"])]
    public function create(Request             $request,
                           PersonalDataService $personalDataService,
                           LoggerInterface     $logger): JsonResponse
    {
        // todo : check user with voter service
        $content = $request->getContent();

        try {
            /** @var User $user */
            $user = $this->getUser();
            /** @var PersonalDataDto $personalDataDto */
            $personalDataDto = $this->serializer->deserialize($content, PersonalDataDto::class, 'json');

            $personalData = $personalDataService->create($personalDataDto, $user);
            $response = $this->jsonResponseProvider->success200(
                $content, $this->serializer->serialize($personalData, 'json')
            );
        } catch (NotEncodableValueException $e) {
            $response = $this->jsonResponseProvider->error400($e->getMessage(), $content);
        } catch (ValidationErrorsInDataException $e) {
            $response = $this->jsonResponseProvider->error422($e->getMessage(), $content);
        } catch (PersonalDataAlreadyExistsAndCannotBeCreatedException $e) {
            $response = $this->jsonResponseProvider->error409($e->getMessage(), $content);
        } catch (\Exception $e) {
            $response = $this->jsonResponseProvider->error500($content);
        } finally {
            if (isset($e))
                $logger->error($e);

        }
        return $response;

    }

    #[Route('/personal_data', name: 'personal_data_create', methods: ["PUT"])]
    public function update(Request             $request,
                           PersonalDataService $personalDataService,
                           LoggerInterface     $logger): JsonResponse
    {
        // todo : check user with voter service
        $content = $request->getContent();

        try {
            /** @var User $user */
            $user = $this->getUser();
            /** @var PersonalDataDto $personalDataDto */
            $personalDataDto = $this->serializer->deserialize($content, PersonalDataDto::class, 'json');

            $personalData = $personalDataService->update($personalDataDto, $user);
            $response = $this->jsonResponseProvider->success200(
                $content, $this->serializer->serialize($personalData, 'json')
            );
        } catch (NotEncodableValueException $e) {
            $response = $this->jsonResponseProvider->error400($e->getMessage(), $content);
        } catch (ValidationErrorsInDataException $e) {
            $response = $this->jsonResponseProvider->error422($e->getMessage(), $content);
        } catch (PersonalDataDoesNotExistsAndCannotBeCreatedHere $e) {
            $response = $this->jsonResponseProvider->error409($e->getMessage(), $content);
        }
        catch (\Exception $e) {
            $response = $this->jsonResponseProvider->error500($content);
        }finally {
            if (isset($e))
                $logger->error($e);

        }
        return $response;

    }

}