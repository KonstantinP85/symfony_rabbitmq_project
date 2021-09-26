<?php

declare(strict_types=1);

namespace App\Manager;

use App\DataProvider\UserDataProvider;
use App\Entity\User;
use App\Exception\AppException;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;
use Symfony\Component\Security\Http\LoginLink\LoginLinkNotification;

class UserManager
{
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    /**
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $passwordEncoder;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var LoginLinkHandlerInterface
     */
    private LoginLinkHandlerInterface $loginLinkHandler;

    /**
     * @var NotifierInterface
     */
    private NotifierInterface $notifier;

    /**
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param LoginLinkHandlerInterface $loginLinkHandler
     * @param NotifierInterface $notifier
     */
    public function __construct(
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder,
        LoginLinkHandlerInterface $loginLinkHandler,
        NotifierInterface $notifier
    ) {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->loginLinkHandler = $loginLinkHandler;
        $this->notifier = $notifier;
    }

    /**
     * Добавление попытки входа
     * @param string $id
     * @return User
     * @throws AppException
     */
    public function addUserLoginAttempt(string $id): User
    {
        $user = $this->get($id);
        $user->addLoginAttempt();
        if ($user->isLoginAttemptsOverLimit()) {
            $user->setActive(false);
        }
        $this->entityManager->flush();

        return $user;
    }

    /**
     * Обнуление количества попыток входа
     * @param string $id
     * @return User
     * @throws AppException
     */
    public function clearUserLoginAttempts(string $id): User
    {
        $user = $this->get($id);
        $user->clearLoginAttempt();
        $this->entityManager->flush();

        return $user;
    }

    /**
     * Получение одного пользователя
     * @param string $id
     * @return User
     * @throws AppException
     */
    public function get(string $id): User
    {
        $user = $this->userRepository->find($id);
        if (!$user instanceof User) {
            throw new AppException('Пользователь не найден', Response::HTTP_BAD_REQUEST);
        }

        return $user;
    }

    /**
     * Получение списка пользователей
     * @return array|User[]
     */
    public function getList(): array
    {
        return $this->userRepository->findAll();
    }

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
     * @return User
     * @throws AppException
     */
    public function create(
        string $firstName,
        string $lastName,
        array $roles,
        string $email,
        string $phone,
        \DateTime $birthday,
        string $gender,
        ?string $patronymic,
        ?string $image
    ): User {
        $user = new User($firstName, $lastName, $roles, $email, $phone, $birthday, $gender, $patronymic, $image);
        $userExist = $this->userRepository->findOneBy(['email' => $email]);
        if ($userExist instanceof User) {
            throw new AppException('Пользователь с таким email уже существует');
        }
        $userExist = $this->userRepository->findOneBy(['phone' => $phone]);
        if ($userExist instanceof User) {
            throw new AppException('Пользователь с таким номером телефона уже существует');
        }
        $this->entityManager->persist($user);

        if (in_array(UserDataProvider::ROLE_ADMIN, $roles)) {
            $user->setActive(true);
        } else {
            $this->sendPasswordLink($user);
        }
        $this->entityManager->flush();

        return $user;
    }

    /**
     * Отправка ссылки для ввода пароля и авторизации
     * @param User $user
     */
    private function sendPasswordLink(User $user)
    {
        $loginLinkDetails = $this->loginLinkHandler->createLoginLink($user);

        $notification = new LoginLinkNotification(
            $loginLinkDetails,
            'Перейдите по сссылке для формирования пароля'
        );

        $recipient = new Recipient($user->getEmail());

        $this->notifier->send($notification, $recipient);
    }

    /**
     * Сохранение указазнного пароля пользователем
     * @param User $user
     * @param string $password
     * @param string $confirmPassword
     * @return User
     * @throws AppException
     */
    public function createPassword(User $user, string $password, string $confirmPassword): User
    {
        if ($password !== $confirmPassword) {
            throw new AppException('Новый пароль и подтверждение не совпадают', Response::HTTP_BAD_REQUEST);
        }
        $encoded = $this->passwordEncoder->encodePassword($user, $password);
        $user->setPassword($encoded);
        $user->setActive(true);
        $this->entityManager->flush();

        return $user;
    }

    /**
     * Блокировка/разблокировка пользователя
     * @param string $id
     * @throws AppException
     */
    public function changeStatus(string $id): void
    {
        $user = $this->get($id);

        if ($user->isActive()) {
            $user->setActive(false);
        } else {
            $user->setActive(true);
        }
        $this->entityManager->flush();
    }

    /**
     * Замена пароля
     * @param User $user
     * @param string $oldPassword
     * @param string $newPassword
     * @param string $confirmPassword
     * @throws AppException
     */
    public function changePassword(User $user, string $oldPassword, string $newPassword, string $confirmPassword)
    {
        if ($newPassword !== $confirmPassword) {
            throw new AppException('Новый пароль и подтверждение не совпадают', Response::HTTP_BAD_REQUEST);
        }
        if ($this->passwordEncoder->isPasswordValid($user, $oldPassword)) {
            $encoded = $this->passwordEncoder->encodePassword($user, $newPassword);
            $user->setPassword($encoded);
            $this->entityManager->flush();
        } else {
            throw new AppException('Старый пароль указан неверно', Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Загрузка фото
     * @param UploadedFile $image
     * @param string $imageUploadDirectory
     * @return string
     * @throws AppException
     */
    public function uploadImage(UploadedFile $image, string $imageUploadDirectory): string
    {
        $filename = uniqid().'.'.$image->guessExtension();
        try {
            $image->move($imageUploadDirectory, $filename);
        }
        catch (FileException $exception){
            throw new AppException($exception->getMessage(), $exception->getCode());
        }

        return $filename;
    }
}