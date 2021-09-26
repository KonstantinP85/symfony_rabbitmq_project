<?php

declare(strict_types=1);

namespace App\DataProvider;

class UserDataProvider
{
    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const LOGIN_ATTEMPTS_LIMIT = 5;
}