<?php

namespace App\Exceptions;

use DomainException;

class JadwalBentrokException extends DomainException
{
    public function __construct(string $message = 'Ruangan sudah digunakan pada waktu tersebut.')
    {
        parent::__construct($message);
    }
}
