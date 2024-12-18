<?php

namespace App\Tests\Repository;

use App\Entity\Connection;
use App\Entity\User;
use App\Factory\UserFactory;
use App\Repository\ConnectionRepository;
use App\Tests\Fixtures\ConnectionFixtures;
use App\Tests\Fixtures\UserFixtures;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ConnectionRepositoryTest extends KernelTestCase
{
    private ?EntityManager $entityManager;
    private ConnectionRepository $repository;
    use UserFixtures, ConnectionFixtures;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->repository = $this->entityManager->getRepository(Connection::class);
    }

    public function testDeleteConnection()
    {
        $this->createUsers();
        $this->createConnections($this->mainUser, $this->users);

        $this->repository->deleteConnection($this->mainUser->_real(), $this->users[0]->_real());
        self::assertNull($this->repository->findOneBy([
            'user' => $this->mainUser->_real(), 'connectedToUser' => $this->users[0]->_real()]));
        self::assertNull($this->repository->findOneBy([
            'connectedToUser' => $this->users[0]->_real(), 'user' => $this->mainUser->_real()]));

    }

    public function testCreateConnection()
    {

        // here I create fixtures
        $this->createUsers();
/*
 * This does not work
 *
        // I don't need to find them again
        // But I do for testing
        $userA = UserFactory::find($this->mainUser->getId());
        $userB = UserFactory::find($this->users[0]->getId());

        // false
        $x = $this->entityManager->contains($userA->_real());
        // false
        $y=  $this->entityManager->contains($userB->_real());

        $this->repository->createConnection($userA, $userB);

        self::assertNotNull($this->repository->findOneBy(['user' => $this->mainUser->_real(), 'connectedToUser' => $this->users[0]->_real()]));
        self::assertNotNull($this->repository->findOneBy(['connectedToUser' => $this->users[0]->_real(), 'user' => $this->mainUser->_real()]));
*/

        // everything after this works

        $userA = $this->entityManager->getRepository(User::class)->find($this->mainUser->getId());
        $userB = $this->entityManager->getRepository(User::class)->find($this->users[0]->getId());

        // true
        $a = $this->entityManager->contains($userA);
        // true
        $b=  $this->entityManager->contains($userB);



        $this->repository->createConnection($userA, $userB);

        self::assertNotNull($this->repository->findOneBy(['user' => $this->mainUser->_real(), 'connectedToUser' => $this->users[0]->_real()]));
        self::assertNotNull($this->repository->findOneBy(['connectedToUser' => $this->users[0]->_real(), 'user' => $this->mainUser->_real()]));

    }
}
