<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\GroupLessonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass=GroupLessonRepository::class)
 * @ORM\Table(name="group_lessons")
 */
class GroupLesson
{
    /**
     * @var string
     * @ORM\Id()
     * @ORM\Column(name="id", type="guid")
     */
    private string $id;

    /**
     * @var string
     * @ORM\Column(name="title", type="string")
     */
    private string $title;

    /**
     * @var string
     * @ORM\Column(name="first_name_trainer", type="string")
     */
    private string $firstNameTrainer;

    /**
     * @var string
     * @ORM\Column(name="last_name_trainer", type="string")
     */
    private string $lastNameTrainer;

    /**
     * @var string|null
     * @ORM\Column(name="patronymic_trainer", type="string", nullable=true)
     */
    private ?string $patronymicTrainer;

    /**
     * @var string
     * @ORM\Column(name="description", length=1000, type="string")
     */
    private string $description;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity=UserGroupLesson::class, mappedBy="groupLesson")
     */
    private Collection $userGroupLessons;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(name="create_time", type="datetime_immutable")
     */
    private \DateTimeImmutable $createTime;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(name="update_time", type="datetime_immutable")
     */
    private \DateTimeImmutable $updateTime;

    public function __construct(
        string $title,
        string $firstNameTrainer,
        string $lastNameTrainer,
        string $description,
        ?string $patronymicTrainer
    ) {
        $this->id = Uuid::uuid4()->toString();
        $this->firstNameTrainer = $firstNameTrainer;
        $this->lastNameTrainer = $lastNameTrainer;
        $this->title = $title;
        $this->description = $description;
        $this->patronymicTrainer = $patronymicTrainer;
        $this->userGroupLessons = new ArrayCollection();
        $date = new \DateTimeImmutable();
        $this->createTime = $date;
        $this->updateTime = $date;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getFirstNameTrainer(): string
    {
        return $this->firstNameTrainer;
    }

    /**
     * @param string $firstNameTrainer
     */
    public function setFirstNameTrainer(string $firstNameTrainer): void
    {
        $this->firstNameTrainer = $firstNameTrainer;
    }

    /**
     * @return string
     */
    public function getLastNameTrainer(): string
    {
        return $this->lastNameTrainer;
    }

    /**
     * @param string $lastNameTrainer
     */
    public function setLastNameTrainer(string $lastNameTrainer): void
    {
        $this->lastNameTrainer = $lastNameTrainer;
    }

    /**
     * @return string|null
     */
    public function getPatronymicTrainer(): ?string
    {
        return $this->patronymicTrainer;
    }

    /**
     * @param string|null $patronymicTrainer
     */
    public function setPatronymicTrainer(?string $patronymicTrainer): void
    {
        $this->patronymicTrainer = $patronymicTrainer;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return Collection|UserGroupLesson[]
     */
    public function getUserGroupLessons(): Collection
    {
        return $this->userGroupLessons;
    }

    /**
     * @param UserGroupLesson $userGroupLesson
     */
    public function addUserGroupLesson(UserGroupLesson $userGroupLesson): void
    {
        if ($this->userGroupLessons->contains($userGroupLesson)) {
            return;
        }

        $this->userGroupLessons->add($userGroupLesson);
        $userGroupLesson->setGroupLesson($this);
    }

    /**
     * @param UserGroupLesson $userGroupLesson
     */
    public function removeGroupLesson(UserGroupLesson $userGroupLesson): void
    {
        if (!$this->userGroupLessons->contains($userGroupLesson)) {
            return;
        }

        $this->userGroupLessons->removeElement($userGroupLesson);
    }

    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdate(): void
    {
        $this->updateTime = new \DateTimeImmutable();
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreateTime(): \DateTimeImmutable
    {
        return $this->createTime;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getUpdateTime(): \DateTimeImmutable
    {
        return $this->updateTime;
    }

    /**
     * @return Collection|UserGroupLesson[]
     */
    public function getUserGroupLessonsWithEmailNotification(): Collection
    {
        $criteria = Criteria::create()
            ->andWhere(Criteria::expr()->eq('notificationType', 'email'));

        return $this->userGroupLessons->matching($criteria);
    }

    /**
     * @return Collection|UserGroupLesson[]
     */
    public function getUserGroupLessonsWithPhoneNotification(): Collection
    {
        $criteria = Criteria::create()
            ->andWhere(Criteria::expr()->eq('notificationType', 'phone'));

        return $this->userGroupLessons->matching($criteria);
    }
}