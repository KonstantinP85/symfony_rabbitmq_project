<?php

declare(strict_types=1);

namespace App\DataProvider;

class NotificationDataProvider
{
    public const NOTIFICATION_EMAIL_SUBJECT = 'От фитнеса';
    public const NOTIFICATION_SMS_URI = 'http://domain.ru/';

    public static function ArrayWhatToChange(): array
    {
        return ['%firstName%', '%lastName%', '%birthday%', '%phone%', '%email%'];
    }
}