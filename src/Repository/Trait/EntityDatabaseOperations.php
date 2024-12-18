<?php

namespace App\Repository\Trait;

trait EntityDatabaseOperations
{

    public function persistAndFlush($entity): void
    {

        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    public function persistOnly($entity): void
    {
        $this->getEntityManager()->persist($entity);
    }

    public function removeAndFlush($entity): void
    {

        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }

    public function removeOnly($entity): void
    {
        $this->getEntityManager()->remove($entity);
    }

    public function flush()
    {
        $this->getEntityManager()->flush();
    }
}