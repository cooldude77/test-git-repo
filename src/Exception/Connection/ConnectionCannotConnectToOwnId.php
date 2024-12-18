<?php

namespace App\Exception\Connection;

class ConnectionCannotConnectToOwnId extends \Exception
{

    public function __construct()
    {
        parent::__construct();
    }
}