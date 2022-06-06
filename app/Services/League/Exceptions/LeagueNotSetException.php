<?php

namespace App\Services\League\Exceptions;

class LeagueNotSetException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('No league is set');
    }
}
