<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\User\UserInterface;
use App\DataProvider\UserDataProvider;

/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="users")
 */
class User implements UserInterface
{
    /**
     * @var string
     * @ORM\Id()
     * @ORM\Column(name="id", type="guid")
     */
    private string $id;

    /**
     * @var string
     * @ORM\Column(name="first_name", type="string")
     */
    private string $firstName;

    /**
     * @var string
     * @ORM\Column(name="last_name", type="string")
     */
    private string $lastName;

    /**
     * @var string|null
     * @ORM\Column(name="patronymic", type="string", nullable=true)
     */
    private ?string $patronymic;

    /**
     * @var array|string[]
     * @ORM\Column(name="roles", type="json")
     */
    private array $roles = [];

    /**
     * @var \DateTime
     * @ORM\Column(name="birthday", type="datetime", length=180)
     */
    private \DateTime $birthday;

    /**
     * @var string
     * @ORM\Column(name="email", type="string", length=180)
     */
    private string $email;

    /**
     * @var string
     * @ORM\Column(name="phone", type="string")
     */
    private string $phone;

    /**
     * @var string
     * @ORM\Column(name="salt", type="string")
     */
    private string $salt;

    /**
     * @var string
     * @ORM\Column(name="password", type="string")
     */
    private string $password;

    /**
     * @var bool
     * @ORM\Column(name="active", type="boolean")
     */
    private bool $active;

    /**
     * @var int
     * @ORM\Column(name="login_attempt_counter", type="integer")
     */
    private int $loginAttemptCounter;

    /**
     * @var string
     * @ORM\Column(name="gender", type="string")
     */
    private string $gender;

    /**
     * @var string|null
     * @ORM\Column(name="image", nullable=true, type="string")
     */
    private ?string $image;

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

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity=UserGroupLesson::class, mappedBy="user")
     */
    private Collection $userGroupLessons;

    /**
     * @param string $firstName
     * @param string $lastName
     * @param array $roles
     * @param string $email
     * @param string $phone
     * @param \DateTime $birthday
     * @param string $gender
     * @param string|null $patronymic
     * @param string|null $image
     */
    public function __construct(
        string $firstName,
        string $lastName,
        array $roles,
        string $email,
        string $phone,
        \DateTime $birthday,
        string $gender,
        ?string $patronymic,
        ?string $image
    ) {
        $this->id = Uuid::uuid4()->toString();
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->salt = md5(uniqid('', true));
        $this->roles = $roles;
        $this->email = $email;
        $this->phone = $phone;
        $this->birthday = $birthday;
        $this->patronymic = $patronymic;
        $this->active = false;
        $this->password = '';
        $this->loginAttemptCounter = 0;
        $this->gender = $gender;
        $this->image = $image;
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
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return \DateTime
     */
    public function getBirthday(): \DateTime
    {
        return $this->birthday;
    }

    /**
     * @param \DateTime $birthday
     */
    public function setBirthday(\DateTime $birthday): void
    {
        $this->birthday = $birthday;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    /**
     * @return string|null
     */
    public function getPatronymic(): ?string
    {
        return $this->patronymic;
    }

    /**
     * @param string|null $patronymic
     */
    public function setPatronymic(?string $patronymic): void
    {
        $this->patronymic = $patronymic;
    }

    /**
     * @return string
     */
    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->email;
    }

    /**
     * @return array|string[]
     */
    public function getRoles(): array
    {
        return array_unique([UserDataProvider::ROLE_USER,...$this->roles]);
    }

    /**
     * @param array $roles
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getSalt(): string
    {
        return $this->salt;
    }

    /**
     * @param string $salt
     */
    public function setSalt(string $salt): void
    {
        $this->salt = $salt;
    }

    public function clearLoginAttempt(): void
    {
        $this->loginAttemptCounter = 0;
    }

    public function addLoginAttempt(): void
    {
        $this->loginAttemptCounter++;
    }

    /**
     * @return bool
     */
    public function isLoginAttemptsOverLimit(): bool
    {
        return $this->loginAttemptCounter > UserDataProvider::LOGIN_ATTEMPTS_LIMIT;
    }

    /**
     * @return string
     */
    public function getGender(): string
    {
        return $this->gender;
    }

    /**
     * @param string $gender
     */
    public function setGender(string $gender): void
    {
        $this->gender = $gender;
    }

    /**
     * @return string|null
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * @param string|null $image
     */
    public function setImage(?string $image): void
    {
        $this->image = $image;
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
     * @param string $role
     * @return bool
     */
    public function isGranted(string $role): bool
    {
        return in_array($role, $this->getRoles());
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
        $userGroupLesson->setUser($this);
    }

    /**
     * @param GroupLesson $groupLesson
     */
    public function removeGroupLesson(GroupLesson $groupLesson): void
    {
        if (!$this->userGroupLessons->contains($groupLesson)) {
            return;
        }

        $this->userGroupLessons->removeElement($groupLesson);
    }

    public function eraseCredentials(): void
    {

    }
}