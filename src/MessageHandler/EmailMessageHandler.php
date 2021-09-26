<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\EmailMessage;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class EmailMessageHandler implements MessageHandlerInterface
{
    /**
     * @var MailerInterface
     */
    private MailerInterface $mailer;

    /**
     * @param MailerInterface $mailer
     */
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param EmailMessage $message
     * @throws TransportExceptionInterface
     */
    public function __invoke(EmailMessage $message)
    {
        $email = (new NotificationEmail())
            ->subject($message->getSubject())
            ->to($message->getEmail())
            ->content($message->getText());
        $this->mailer->send($email);
    }
}