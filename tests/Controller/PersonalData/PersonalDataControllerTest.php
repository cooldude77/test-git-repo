<?php

namespace App\Tests\Controller\PersonalData;

use App\Factory\PersonalDataFactory;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Browser;
use Zenstruck\Browser\Test\HasBrowser;

class PersonalDataControllerTest extends WebTestCase
{

    use HasBrowser;

    public function testCreateHappyPath()
    {
        $user = UserFactory::createOne(['roles' => ['ROLE_USER']]);
        $this->browser()->post('api/v1/personal_data',
            [
                'headers' => ['auth-token' => $user->getAuthToken()],
                'json' =>
                    ['firstName' => 'Ashvini', 'lastName' => 'Saxena', 'middleName' => 'Kumar', 'aboutMe' => 'I live']]
        )
            ->use(function (Browser $browser) {
                $content = json_decode($browser->content(), true);

                // todo check this
                /*

                ,
                            'lastSyncDateAndTime' =>
                                array(
                                    'date' => '2024-12-15 04:38:25.355543',
                                    'timezone_type' => 3,
                                    'timezone' => 'UTC',
                                ),

                 */
                unset($content['meta']['lastSyncDateAndTime']);
                $expected = array(
                    'meta' =>
                        array(
                            'status' => 'Success',
                            'httpCode' => 200,
                            'requestData' => [
                                'firstName' => 'Ashvini',
                                'middleName' => 'Kumar',
                                'lastName' => 'Saxena',
                                'aboutMe' => 'I live']
                        ),
                    'content' =>
                        array(
                            'firstName' => 'Ashvini',
                            'middleName' => 'Kumar',
                            'lastName' => 'Saxena',
                            'givenName' => NULL,
                            'aboutMe' => 'I live'
                        ),
                );
                $this->assertEquals($content, $expected);

            }
            )
            ->assertStatus(200);


    }

    public function testWrongInputData()
    {

    }

    public function testWhenRecordAlreadyExists()
    {
        $user = UserFactory::createOne(['roles' => ['ROLE_USER']]);

        PersonalDataFactory::createOne(['user' => $user->_real()]);

        $this->browser()->post('api/v1/personal_data',
            [
                'headers' => ['auth-token' => $user->getAuthToken()],
                'json' =>
                    ['firstName' => 'Ashvini', 'lastName' => 'Saxena', 'middleName' => 'Kumar', 'aboutMe' => 'I live']]
        )
            ->assertStatus(Response::HTTP_CONFLICT);
        // todo :check response legitimate
    }

    public function testUpdateHappyPath()
    {
        $user = UserFactory::createOne(['roles' => ['ROLE_USER']]);

        PersonalDataFactory::createOne(['user' => $user]);

        $this->browser()->put('api/v1/personal_data',
            [
                'headers' => ['auth-token' => $user->getAuthToken()],
                'json' =>
                    ['firstName' => 'Ashvini', 'lastName' => 'Saxena', 'middleName' => 'Kumar', 'aboutMe' => 'I live']]
        )
            ->assertStatus(Response::HTTP_OK);
        // todo :check response legitimate
    }
}
