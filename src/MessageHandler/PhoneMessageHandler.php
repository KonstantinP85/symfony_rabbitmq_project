<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\DataProvider\NotificationDataProvider;
use App\Exception\AppException;
use App\Exception\ServiceException;
use App\Message\PhoneMessage;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class PhoneMessageHandler implements MessageHandlerInterface
{
    /**
     * @param PhoneMessage $message
     * @throws ServiceException|AppException
     */
    public function __invoke(PhoneMessage $message)
    {
        $client = new HttpClient();
        try {
            $uri = NotificationDataProvider::NOTIFICATION_SMS_URI . '?phone=' . $message->getPhone() . '&message=' . $message->getText();
            $request = $client->get($uri);
            if ($request->getStatusCode() !== Response::HTTP_OK) {
                throw new AppException();
            }
        } catch (GuzzleException $e) {
            throw new ServiceException($e);
        }
    }
}