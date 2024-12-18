<?php

namespace App\Exception\Connection;

class ConnectionAlreadyExistsBetweenUsers extends \Exception
{

    public function __construct()
    {
        parent::__construct();
    }
}