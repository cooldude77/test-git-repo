<?php

namespace App\Exception\Connection;

class ConnectionDoesNotExistBetweenUsers extends \Exception
{

    public function __construct()
    {
        parent::__construct();
    }
}