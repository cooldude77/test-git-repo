<?php

namespace App\Controller\Authentication;

use App\DTO\Authentication\SignUpDTO;
use App\Event\User\UserCreatedEvent;
use App\Exception\General\UniqueEntityFoundException;
use App\Service\Response\JsonResponseProvider;
use App\Service\User\UserService;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/v1', name: 'api_v1_users_')]
class SignUpController extends AbstractController
{
    public function __construct(private readonly JsonResponseProvider     $jsonResponseProvider,
                                private readonly SerializerInterface      $serializer,
                                private readonly EventDispatcherInterface $dispatcher)
    {
    }

    #[Route('/sign_up', name: 'sign_up', methods: ['POST'])]
    public function create(Request         $request,
                           UserService     $serviceUser,
                           LoggerInterface $logger): JsonResponse
    {
        $content = $request->getContent();

        try {
            $signUpDto = $this->serializer->deserialize($content, SignUpDTO::class, 'json');

            $user = $serviceUser->create($signUpDto);
            $this->dispatcher->dispatch(new UserCreatedEvent($user), UserCreatedEvent::USER_CREATED);

            $response = $this->jsonResponseProvider->requestData($content)
                ->success200($content, $this->serializer->serialize($user, 'json'));
        } catch (UniqueEntityFoundException $e) {
            $response = $this->jsonResponseProvider->requestData($content)->error422($e->getMessage(), $content);
        } catch (NotEncodableValueException $e) {
            $response = $this->jsonResponseProvider->requestData($content)->error400($e->getMessage(), $content);
        } catch (
        Exception $e) {
            $response = $this->jsonResponseProvider->requestData($content)->Error500($content);
        } finally {
            if (isset($e))
                $logger->error($e);

        }
        return $response;

    }


}