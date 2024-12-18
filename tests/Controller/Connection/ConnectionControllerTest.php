<?php

namespace App\Tests\Controller\Connection;

use App\Factory\PersonalDataFactory;
use App\Tests\Fixtures\ConnectionFixtures;
use App\Tests\Fixtures\UserFixtures;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Browser\Test\HasBrowser;

class ConnectionControllerTest extends WebTestCase
{

    use HasBrowser,
        UserFixtures,
        ConnectionFixtures;


    public function testGetConnectionPaged()
    {

        $this->createUsers();
        $this->createConnections($this->mainUser, $this->users);
        PersonalDataFactory::createOne(['user' => $this->users[0]]);


        $this->browser()->GET('api/v1/connections/1',
            [
                'headers' => ['auth-token' => $this->mainUser->getAuthToken()],
                'json' =>
                    ['serverId' => $this->users[0]->getId()]]
        )
            ->assertStatus(Response::HTTP_OK);

        $this->browser()->GET('api/v1/connections/2',
            [
                'headers' => ['auth-token' => $this->mainUser->getAuthToken()],
                'json' =>
                    ['serverId' => $this->users[0]->getId()]]
        )
            ->assertStatus(Response::HTTP_OK);

        $this->browser()->GET('api/v1/connections/3',
            [
                'headers' => ['auth-token' => $this->mainUser->getAuthToken()],
                'json' =>
                    ['serverId' => $this->users[0]->getId()]]
        )
            ->assertStatus(Response::HTTP_OK);
    }


    public function testCreateConnection()
    {
        $this->createUsers();
        $this->browser()->post('api/v1/connections',
            [
                'headers' => ['auth-token' => $this->mainUser->getAuthToken()],
                'json' =>
                    ['serverId' => $this->users[0]->getId()]]
        )
            ->assertStatus(Response::HTTP_OK);

    }

    public function testDeleteConnection()
    {

        $this->createUsers();
        $this->createConnections($this->mainUser, $this->users);
        $this->browser()->DELETE('api/v1/connections',
            [
                'headers' => ['auth-token' => $this->mainUser->getAuthToken()],
                'json' =>
                    ['serverId' => $this->users[0]->getId()]]
        )
            ->assertStatus(Response::HTTP_OK);


    }
}
