<?php

declare(strict_types=1);

namespace App\DtoModel;

use Symfony\Component\Validator\Constraints as Assert;

class GroupLessonDtoModel
{
    /**
     * @var string
     * @Assert\NotBlank(message="Title is required")
     */
    public string $title;

    /**
     * @var string
     * @Assert\NotBlank(message="First name is required")
     */
    public string $firstNameTrainer;

    /**
     * @var string
     * @Assert\NotBlank(message="Last name is required")
     */
    public string $lastNameTrainer;

    /**
     * @var string
     * @Assert\NotBlank(message="Description is required")
     */
    public string $description;

    /**
     * @var string|null
     */
    public ?string $patronymicTrainer = null;
}