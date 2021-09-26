<?php

declare(strict_types=1);

namespace App\DtoModel;

use Symfony\Component\Validator\Constraints as Assert;

class CreateUserDtoModel extends UpdateUserDtoModel
{
    /**
     * @var string
     * @Assert\Email(message="Not corect email")
     * @Assert\NotBlank(message="Email is required")
     */
    public string $email;
}