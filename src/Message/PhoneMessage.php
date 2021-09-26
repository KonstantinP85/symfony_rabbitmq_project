<?php

declare(strict_types=1);

namespace App\Message;

class PhoneMessage
{
    /**
     * @var string
     */
    private string $phone;

    /**
     * @var string
     */
    private string $text;

    /**
     * @param string $phone
     * @param string $text
     */
    public function __construct(string $phone, string $text)
    {
        $this->phone = $phone;
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }
}