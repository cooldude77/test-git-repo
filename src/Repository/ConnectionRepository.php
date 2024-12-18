<?php /** @noinspection ALL */

namespace App\Repository;

use App\Entity\Connection;
use App\Entity\User;
use App\Repository\Trait\EntityDatabaseOperations;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Connection>
 */
class ConnectionRepository extends ServiceEntityRepository
{
    use EntityDatabaseOperations;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Connection::class);
    }

    //    /**
    //     * @return Connection[] Returns an array of Connection objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Connection
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function createConnection(User $userA, User $userB): void
    {

        $connectionA = new Connection();
        $connectionA->setUser($userA);
        $connectionA->setConnectedToUser($userB);


        $connectionB = new Connection();
        $connectionB->setUser($userB);
        $connectionB->setConnectedToUser($userA);

        $this->persistOnly($connectionA);
        $this->persistOnly($connectionB);
        $this->flush();
    }

    public function deleteConnection(User $user, User $connectionUser)
    {
        $connectionA = $this->findOneBy(['user' => $user, 'connectedToUser' => $connectionUser]);
        $connectionB = $this->findOneBy(['user' => $connectionUser, 'connectedToUser' => $user]);

        $this->removeOnly($connectionA);
        $this->removeOnly($connectionB);
        $this->flush();

    }

    public function getQueryForPagination(User $user)
    {

        return $this->getEntityManager()
            ->createQueryBuilder("cu")
            ->select("c","cu","pd")
            ->from(Connection::class, 'c')
            ->innerJoin('c.connectedToUser', "cu")
            ->leftJoin("cu.personalData","pd")
            ->where("c.user=:u")
            ->setParameter("u", $user)
            ->getQuery()
            ->setFetchMode(User::class, "abcd", ClassMetadata::FETCH_EAGER);
    }
}
