<?php

declare(strict_types=1);

namespace App\Message;

class EmailMessage
{
    /**
     * @var string
     */
    private string $email;

    /**
     * @var string
     */
    private string $subject;

    /**
     * @var string
     */
    private string $text;

    /**
     * @param string $email
     * @param string $text
     * @param string $subject
     */
    public function __construct(string $email, string $text, string $subject)
    {
        $this->email = $email;
        $this->subject = $subject;
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }
}