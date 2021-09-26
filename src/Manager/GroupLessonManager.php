<?php

declare(strict_types=1);

namespace App\Manager;

use App\DataProvider\NotificationDataProvider;
use App\Entity\GroupLesson;
use App\Entity\User;
use App\Entity\UserGroupLesson;
use App\Exception\AppException;
use App\Message\EmailMessage;
use App\Message\PhoneMessage;
use App\Repository\GroupLessonRepository;
use App\Repository\UserGroupLessonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

class GroupLessonManager
{
    /**
     * @var GroupLessonRepository
     */
    private GroupLessonRepository $groupLessonRepository;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var UserGroupLessonRepository
     */
    private UserGroupLessonRepository $userGroupLessonRepository;

    /**
     * @var MessageBusInterface
     */
    private MessageBusInterface $bus;

    /**
     * @param GroupLessonRepository $groupLessonRepository
     * @param UserGroupLessonRepository $userGroupLessonRepository
     * @param EntityManagerInterface $entityManager
     * @param MessageBusInterface $bus
     */
    public function __construct(
        GroupLessonRepository $groupLessonRepository,
        UserGroupLessonRepository $userGroupLessonRepository,
        EntityManagerInterface $entityManager,
        MessageBusInterface $bus
    ) {
        $this->userGroupLessonRepository = $userGroupLessonRepository;
        $this->groupLessonRepository = $groupLessonRepository;
        $this->entityManager = $entityManager;
        $this->bus = $bus;
    }

    /**
     * @return array
     */
    public function getListForAdmin(): array
    {
        $groupLessonsList = $this->groupLessonRepository->findAll();
        $result = [];
        foreach ($groupLessonsList as $groupLesson) {
            if ($groupLesson instanceof GroupLesson) {
                $result[] = [
                    'id' => $groupLesson->getId(),
                    'title' => $groupLesson->getTitle(),
                    'firstNameTrainer' => $groupLesson->getFirstNameTrainer(),
                    'lastNameTrainer' => $groupLesson->getLastNameTrainer(),
                    'patronymicTrainer' => $groupLesson->getPatronymicTrainer(),
                    'description' => $groupLesson->getDescription(),
                    'participantsNumber' => $groupLesson->getUserGroupLessons()->count(),
                ];
            }
        }

        return $result;
    }
    /**
     * @param User $user
     * @return array
     */
    public function getListForUser(User $user): array
    {
        $groupLessonsList = $this->groupLessonRepository->findAll();
        $result = [];
        foreach ($groupLessonsList as $groupLesson) {
            if ($groupLesson instanceof GroupLesson) {
                $userGroupLesson = $this->userGroupLessonRepository->findOneBy(['user' => $user, 'groupLesson' => $groupLesson]);
                $notificationType = $userGroupLesson instanceof UserGroupLesson ? $userGroupLesson->getNotificationType() : 'notSubscribe';
                $result[] = [
                    'id' => $groupLesson->getId(),
                    'title' => $groupLesson->getTitle(),
                    'firstNameTrainer' => $groupLesson->getFirstNameTrainer(),
                    'lastNameTrainer' => $groupLesson->getLastNameTrainer(),
                    'patronymicTrainer' => $groupLesson->getPatronymicTrainer(),
                    'description' => $groupLesson->getDescription(),
                    'notificationType' => $notificationType
                ];
            }
        }

        return $result;
    }

    /**
     * @param string $title
     * @param string $firstNameTrainer
     * @param string $lastNameTrainer
     * @param string $description
     * @param string|null $patronymicTrainer
     */
    public function create(
        string  $title,
        string  $firstNameTrainer,
        string  $lastNameTrainer,
        string  $description,
        ?string $patronymicTrainer
    ) {
        $groupLesson = new GroupLesson($title, $firstNameTrainer, $lastNameTrainer, $description, $patronymicTrainer);
        $this->entityManager->persist($groupLesson);
        $this->entityManager->flush();
    }

    /**
     * @param string $id
     * @param string $title
     * @param string $firstNameTrainer
     * @param string $lastNameTrainer
     * @param string $description
     * @param string|null $patronymicTrainer
     * @throws AppException
     */
    public function update(
        string  $id,
        string  $title,
        string  $firstNameTrainer,
        string  $lastNameTrainer,
        string  $description,
        ?string $patronymicTrainer
    ) {
        $groupLesson = $this->get($id);
        $groupLesson->setTitle($title);
        $groupLesson->setFirstNameTrainer($firstNameTrainer);
        $groupLesson->setLastNameTrainer($lastNameTrainer);
        $groupLesson->setDescription($description);
        $groupLesson->setPatronymicTrainer($patronymicTrainer);

        $this->entityManager->flush();
    }

    /**
     * @param string $id
     * @return GroupLesson
     * @throws AppException
     */
    public function get(string $id): GroupLesson
    {
        $groupLesson = $this->groupLessonRepository->find($id);
        if (!$groupLesson instanceof GroupLesson) {
            throw new AppException('Занятие не найдено', Response::HTTP_BAD_REQUEST);
        }

        return $groupLesson;
    }

    /**
     * @param string $id
     * @throws AppException
     */
    public function remove(string $id): void
    {
        $groupLesson = $this->get($id);
        $this->entityManager->remove($groupLesson);
        $this->entityManager->flush();
    }

    /**
     * @param User $user
     * @param string $id
     * @throws AppException
     */
    public function add(User $user, string $id): void
    {
        $groupLesson = $this->get($id);

        $userGroupLesson = new UserGroupLesson($user, $groupLesson, null);
        $this->entityManager->persist($userGroupLesson);
        $this->entityManager->flush();
    }

    /**
     * @param User $user
     * @param string $id
     * @throws AppException
     */
    public function cancel(User $user, string $id): void
    {
        $groupLesson = $this->get($id);
        $userGroupLesson = $this->userGroupLessonRepository->findOneBy(['user' => $user, 'groupLesson' => $groupLesson]);
        if (!$userGroupLesson instanceof UserGroupLesson) {
            throw new AppException('Подписка не найдена', Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->remove($userGroupLesson);
        $this->entityManager->flush();
    }

    /**
     * @param User $user
     * @param string $id
     * @param string|null $notificationType
     * @throws AppException
     */
    public function changeNotification(User $user, string $id, ?string $notificationType): void
    {
        $groupLesson = $this->get($id);
        $userGroupLesson = $this->userGroupLessonRepository->findOneBy(['user' => $user, 'groupLesson' => $groupLesson]);
        if (!$userGroupLesson instanceof UserGroupLesson) {
            throw new AppException('Подписка не найдена', Response::HTTP_BAD_REQUEST);
        }

        $userGroupLesson->setNotificationType($notificationType);
        $this->entityManager->flush();
    }

    /**
     * @param string $smsMessage
     * @param string $id
     * @throws AppException
     */
    public function sendSmsMessage(string $smsMessage, string $id)
    {
        $groupLesson = $this->get($id);
        foreach ($groupLesson->getUserGroupLessonsWithPhoneNotification() as $userGroupLesson) {
            $smsMessageConvert = str_replace(
                NotificationDataProvider::ArrayWhatToChange(),
                [
                    $userGroupLesson->getUser()->getFirstName(),
                    $userGroupLesson->getUser()->getLastName(),
                    $userGroupLesson->getUser()->getBirthday()->format('Y-m-d'),
                    $userGroupLesson->getUser()->getPhone(),
                    $userGroupLesson->getUser()->getEmail(),
                ],
                $smsMessage
            );
            $this->bus->dispatch(new PhoneMessage($userGroupLesson->getUser()->getEmail(), $smsMessageConvert));
        }
    }

    /**
     * @param string $emailMessage
     * @param string $id
     * @throws AppException
     */
    public function sendEmailMessage(string $emailMessage, string $id)
    {
        $groupLesson = $this->get($id);
        foreach ($groupLesson->getUserGroupLessonsWithEmailNotification() as $userGroupLesson) {
            $emailMessageConvert = str_replace(
                NotificationDataProvider::ArrayWhatToChange(),
                [
                    $userGroupLesson->getUser()->getFirstName(),
                    $userGroupLesson->getUser()->getLastName(),
                    $userGroupLesson->getUser()->getBirthday()->format('Y-m-d'),
                    $userGroupLesson->getUser()->getPhone(),
                    $userGroupLesson->getUser()->getEmail(),
                ],
                $emailMessage
            );
            $this->bus->dispatch(new EmailMessage($userGroupLesson->getUser()->getEmail(), $emailMessageConvert, NotificationDataProvider::NOTIFICATION_EMAIL_SUBJECT));
        }
    }
}