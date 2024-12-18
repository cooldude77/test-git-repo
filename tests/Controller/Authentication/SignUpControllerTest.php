<?php

namespace App\Tests\Controller\Authentication;

use App\Entity\User;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Browser\Test\HasBrowser;

class SignUpControllerTest extends WebTestCase
{
    use HasBrowser;

    public function testHappySignUp()
    {
        $this->browser()->post('api/v1/sign_up', [
            'json' => ['email' => 'test@test.com', 'password' => 'asdacadfqwefwefw']

        ])
            ->assertStatus(200);
        /** @var User $user */
        $user = UserFactory::find(['email'=>'test@test.com']);

        $this->assertNotNull($user);
        $this->assertEquals(['ROLE_USER'], $user->getRoles());

    }

    public function testEmailSentOnSignUp()
    {

        // TODO
    }

    public function testIfRightHttpParametersAreUsed()
    {

        // get cannot be used on this
        $this->browser()->get('api/v1/users/sign_up')
            ->assertStatus(Response::HTTP_NOT_FOUND);

    }

    public function testIncorrectUniqueEmail()
    {
        // TODO
    }

    public function testWrongDataInFields()
    {
        // TODO
    }

    public function testLowStrengthPassword()
    {
        // TODO
    }

}
