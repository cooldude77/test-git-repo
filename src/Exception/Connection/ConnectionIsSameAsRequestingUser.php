<?php

namespace App\Exception\Connection;

class ConnectionIsSameAsRequestingUser extends \Exception
{

    public function __construct()
    {
        parent::__construct();
    }
}