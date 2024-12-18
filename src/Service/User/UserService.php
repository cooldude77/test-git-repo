<?php

namespace App\Service\User;

use App\DTO\Authentication\SignUpDTO;
use App\Entity\User;
use App\Exception\General\UniqueEntityFoundException;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\ByteString;

readonly class UserService
{

    public function __construct(private UserRepository              $userRepository,
                                private UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    /**
     * @throws UniqueEntityFoundException
     */
    public function create(SignUpDTO $signUpDTO): User
    {

        if ($this->userRepository->findOneBy(['email' => $signUpDTO->email]))
            throw new UniqueEntityFoundException("$signUpDTO->email");

        $user = new User();
        $user->setEmail($signUpDTO->email);

        $hash = $this->userPasswordHasher->hashPassword($user, $signUpDTO->password);
        $user->setPassword($hash);
        $user->setAuthToken(ByteString::fromRandom(32, md5($hash)));
        $user->setUserCode(rand(1000000, 9999999));
        $user->setRoles(['ROLE_USER']);

        $this->userRepository->persistAndFlush($user);
        return $user;
    }
}