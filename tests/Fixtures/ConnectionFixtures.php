<?php

namespace App\Tests\Fixtures;

use App\Factory\ConnectionFactory;

trait ConnectionFixtures
{
    public array $connections;

    public function createConnections($mainUser, $users): void
    {
        // 20 users, 15 connected
        for ($i = 0; $i < 15; $i++) {

            ConnectionFactory::createOne(['user' => $mainUser, 'connectedToUser' => $users[$i]]);
            // ONE ENTRY FOR REVERSE TOO
            ConnectionFactory::createOne(['user' => $users[$i], 'connectedToUser' => $mainUser]);
        }

    }
}