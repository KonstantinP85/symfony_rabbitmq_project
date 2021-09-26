<?php

declare(strict_types=1);

namespace App\DtoModel;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateUserDtoModel extends BaseDtoModel
{
    /**
     * @var string
     * @Assert\NotBlank(message="First name is required")
     */
    public string $firstName;

    /**
     * @var string
     * @Assert\NotBlank(message="Last name is required")
     */
    public string $lastName;

    /**
     * @var string
     * @Assert\NotBlank(message="Phone is required")
     */
    public string $phone;

    /**
     * @var \DateTime
     * @Assert\Type(type="datetime")
     */
    public \DateTime $birthday;

    /**
     * @var string
     * @Assert\NotBlank(message="Gender is required")
     * @Assert\Choice({"male", "female"})
     */
    public string $gender;

    /**
     * @var string|null
     */
    public ?string $patronymic = null;

    /**
     * @var string|null
     * @Assert\File()
     */
    public ?string $image = null;
}