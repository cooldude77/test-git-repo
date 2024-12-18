<?php

namespace App\Service\Connection;

use App\DTO\ConnectionData\ConnectionDto;
use App\Entity\User;
use App\Exception\Connection\ConnectionAlreadyExistsBetweenUsers;
use App\Exception\Connection\ConnectionCannotConnectToOwnId;
use App\Exception\Connection\ConnectionDoesNotExistBetweenUsers;
use App\Exception\Connection\ConnectionIsSameAsRequestingUser;
use App\Exception\Connection\UserNotFoundForMakingConnection;
use App\Repository\ConnectionRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\Query;

readonly class ConnectionService
{

    public function __construct(
        private UserRepository       $userRepository,
        private ConnectionRepository $connectionRepository)
    {
    }

    /**
     * @throws ConnectionCannotConnectToOwnId
     * @throws UserNotFoundForMakingConnection|ConnectionAlreadyExistsBetweenUsers
     */
    public function create(ConnectionDto $connectionDto, User $user): void
    {
        /** @var User $connectionUser */
        $connectionUser = $this->userRepository->find($connectionDto->serverId);

        if ($connectionUser == null)
            throw new UserNotFoundForMakingConnection();

        if ($connectionUser->getId() == $user->getId())
            throw new ConnectionCannotConnectToOwnId();

        if ($this->connectionRepository->findOneBy(['user' => $user, 'connectedToUser' => $connectionUser]) != null)
            throw new ConnectionAlreadyExistsBetweenUsers();


        $this->connectionRepository->createConnection($user, $connectionUser);
    }

    /**
     * @param ConnectionDto $connectionDto
     * @param User $user
     * @return void
     * @throws ConnectionDoesNotExistBetweenUsers
     * @throws ConnectionIsSameAsRequestingUser
     * @throws UserNotFoundForMakingConnection
     */
    public function delete(ConnectionDto $connectionDto, User $user): void
    {
        /** @var User $connectionUser */
        $connectionUser = $this->userRepository->find($connectionDto->serverId);

        if ($connectionUser == null)
            throw new UserNotFoundForMakingConnection();

        if ($connectionUser->getId() == $user->getId())
            throw new ConnectionIsSameAsRequestingUser();

        if ($this->connectionRepository->findOneBy(['user' => $user, 'connectedToUser' => $connectionUser]) == null)
            throw new ConnectionDoesNotExistBetweenUsers();


        $this->connectionRepository->deleteConnection($user, $connectionUser);
    }

    public function getPaginationQuery(User $user):Query
    {
      return  $this->connectionRepository->getQueryForPagination($user);
    }
}