<?php

namespace App\Controller\Connection;

use App\DTO\ConnectionData\ConnectionDto;
use App\Entity\User;
use App\Exception\Connection\ConnectionAlreadyExistsBetweenUsers;
use App\Exception\Connection\ConnectionCannotConnectToOwnId;
use App\Exception\Connection\ConnectionDoesNotExistBetweenUsers;
use App\Exception\Connection\ConnectionIsSameAsRequestingUser;
use App\Exception\Connection\UserNotFoundForMakingConnection;
use App\Service\Connection\ConnectionService;
use App\Service\Response\JsonResponseProvider;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/v1', name: 'api_v1_connections_')]
class ConnectionController extends AbstractController
{
    const MAX_RECORDS_PER_PAGE = 10;

    public function __construct(private readonly SerializerInterface  $serializer,
                                private readonly JsonResponseProvider $jsonResponseProvider,
                                private readonly LoggerInterface      $logger,
                                private readonly ConnectionService    $connectionService,
    )
    {


    }

    #[Route('/connections', name: 'create', methods: ['POST'])]
    public function createConnection(Request $request): JsonResponse
    {
        // todo : check user with voter service
        $content = $request->getContent();

        try {
            /** @var User $user */
            $user = $this->getUser();

            /** @var ConnectionDto $connectionDto */
            $connectionDto = $this->serializer->deserialize($content, ConnectionDto::class, 'json');
            $this->connectionService->create($connectionDto, $user);
            $response = $this->jsonResponseProvider->success200($content);

        } catch (
        ConnectionAlreadyExistsBetweenUsers
        |ConnectionCannotConnectToOwnId
        |UserNotFoundForMakingConnection $e
        ) {
            $response = $this->jsonResponseProvider->error422($e->getMessage(), $content);
        } catch (Exception $e) {
            $response = $this->jsonResponseProvider->Error500($content);
        } finally {
            if (isset($e))
                $this->logger->error($e);

        }
        return $response;
    }


    #[Route('/connections', name: 'delete', methods: ['DELETE'])]
    public function deleteConnection(Request $request): JsonResponse
    {
        // todo : check user with voter service
        $content = $request->getContent();

        try {
            /** @var User $user */
            $user = $this->getUser();

            /** @var ConnectionDto $connectionDto */
            $connectionDto = $this->serializer->deserialize($content, ConnectionDto::class, 'json');
            $this->connectionService->delete($connectionDto, $user);
            $response = $this->jsonResponseProvider->success200($content, []);

        } catch (
        ConnectionIsSameAsRequestingUser
        |ConnectionDoesNotExistBetweenUsers
        |UserNotFoundForMakingConnection $e
        ) {
            $response = $this->jsonResponseProvider->error422($e->getMessage(), $content);
        } catch (Exception $e) {
            $response = $this->jsonResponseProvider->Error500($content);
        } finally {
            if (isset($e))
                $this->logger->error($e);

        }
        return $response;
    }

    #[Route('/connections/{page}', name: 'paginated', methods: ['GET'])]
    public function getConnectionPaged(int $page, PaginatorInterface $paginator): JsonResponse
    {

        /** @var User $user */
        $user = $this->getUser();

        $query = $this->connectionService->getPaginationQuery($user);

        $paginatedData = $paginator->paginate($query, $page, self::MAX_RECORDS_PER_PAGE);

        $responseData = json_decode($this->serializer->serialize($paginatedData, 'json'), true);


        return $this->jsonResponseProvider
            ->builder()
            ->status(JsonResponseProvider::SUCCESS)
            ->httpCode(Response::HTTP_OK)
            ->requestData(['page' => $page])
            ->content($responseData)
            ->addExtraToMeta('page', $paginatedData->getCurrentPageNumber())
            ->addExtraToMeta('totalRecords', $paginatedData->getTotalItemCount())
            ->addExtraToMeta('itemsPerPage', $paginatedData->getItemNumberPerPage())
            ->build();
    }

}