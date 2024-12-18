<?php

namespace App\Event\User;

use App\Entity\User;

class UserCreatedEvent
{
    const USER_CREATED = "USER_CREATED";

    /**
     * @param User $user
     */
    public function __construct(private readonly User $user)
    {
    }
}