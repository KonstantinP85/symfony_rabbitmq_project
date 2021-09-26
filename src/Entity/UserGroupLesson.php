<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserGroupLessonRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity(repositoryClass=UserGroupLessonRepository::class)
 * @ORM\Table(name="user_group_lesson")
 */
class UserGroupLesson
{
    /**
     * @var string
     * @ORM\Id()
     * @ORM\Column(name="id", type="guid")
     */
    private string $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="userGroupLessons")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private User $user;

    /**
     * @ORM\ManyToOne(targetEntity=GroupLesson::class, inversedBy="userGroupLessons")
     * @ORM\JoinColumn(name="group_lesson_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private GroupLesson $groupLesson;

    /**
     * @var string|null
     * @ORM\Column(name="notification_type", type="string", nullable=true)
     */
    private ?string $notificationType;

    /**
     * @param User $user
     * @param GroupLesson $groupLesson
     * @param string|null $notificationType
     */
    public function __construct(User $user, GroupLesson $groupLesson, ?string $notificationType)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->user = $user;
        $this->groupLesson = $groupLesson;
        $this->notificationType = $notificationType;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        if ($this->user === $user) {
            return;
        }

        $this->user = $user;
        $user->addUserGroupLesson($this);
    }

    /**
     * @return GroupLesson
     */
    public function getGroupLesson(): GroupLesson
    {
        return $this->groupLesson;
    }

    /**
     * @param GroupLesson $groupLesson
     */
    public function setGroupLesson(GroupLesson $groupLesson): void
    {
        if ($this->groupLesson === $groupLesson) {
            return;
        }

        $this->groupLesson = $groupLesson;
        $groupLesson->addUserGroupLesson($this);
    }

    /**
     * @return string|null
     */
    public function getNotificationType(): ?string
    {
        return $this->notificationType;
    }

    /**
     * @param string|null $notificationType
     */
    public function setNotificationType(?string $notificationType): void
    {
        $this->notificationType = $notificationType;
    }
}