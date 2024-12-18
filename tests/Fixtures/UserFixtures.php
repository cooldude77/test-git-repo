<?php

namespace App\Tests\Fixtures;

use App\Factory\UserFactory;

trait UserFixtures
{
    public array $users;
    /**
     * @var \App\Entity\User|mixed|object|(object&\Zenstruck\Foundry\Persistence\Proxy)|\Zenstruck\Foundry\Persistence\Proxy
     */
    public mixed $mainUser;

    public function createUsers(): void
    {
        $this->mainUser = UserFactory::createOne(['roles' => ['ROLE_USER']]);

        $this->users = UserFactory::createMany(20, ['roles' => ['ROLE_USER']]);

    }
}