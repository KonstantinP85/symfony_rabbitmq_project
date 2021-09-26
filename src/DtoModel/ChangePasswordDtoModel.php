<?php

declare(strict_types=1);

namespace App\DtoModel;

use Symfony\Component\Validator\Constraints as Assert;

class ChangePasswordDtoModel
{
    /**
     * @var string
     * @Assert\NotBlank(message="Password is required")
     */
    public string $oldPassword;

    /**
     * @var string
     * @Assert\NotBlank(message="Password is required")
     * @Assert\Length(
     *     min="5",
     *     minMessage="Password length is limited to {{ limit }} characters"
     * )
     */
    public string $newPassword;

    /**
     * @var string
     * @Assert\NotBlank(message="Confirm password is required")
     */
    public string $confirmPassword;
}