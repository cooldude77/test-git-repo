<?php

namespace App\Exception\Connection;

class UserNotFoundForMakingConnection extends \Exception
{

    public function __construct()
    {
        parent::__construct();
    }
}