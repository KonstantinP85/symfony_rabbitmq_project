<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Exception\AppException;
use App\Exception\ServiceException;
use App\Manager\GroupLessonManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GroupLessonController extends UserController
{
    /**
     * @Route("/user/groupLessons", name="user_group_lesson")
     * @param GroupLessonManager $groupLessonManager
     * @return Response
     */
    public function list(GroupLessonManager $groupLessonManager): Response
    {
        try {
            $user = $this->getUser();
            if (!$user instanceof User) {
                throw new AppException("Auth error", Response::HTTP_UNAUTHORIZED);
            }
            $groupLessons = $groupLessonManager->getListForUser($user);

        } catch (AppException $e) {
            throw new ServiceException($e);
        }

        return $this->render('user/groupLesson.html.twig', [
            'groupLessons' => $groupLessons,
        ]);
    }

    /**
     * @Route("/user/groupLessons/{id}/add", name="user_group_lesson_add")
     * @param GroupLessonManager $groupLessonManager
     * @param string $id
     * @return Response
     */
    public function addSubscribe(GroupLessonManager $groupLessonManager, string $id): Response
    {
        try {
            $user = $this->getUser();
            if (!$user instanceof User) {
                throw new AppException("Auth error", Response::HTTP_UNAUTHORIZED);
            }
            $groupLessonManager->add($user, $id);

        } catch (AppException $e) {
            throw new ServiceException($e);
        }

        return $this->redirectToRoute('user_group_lesson');
    }

    /**
     * @Route("/user/groupLessons/{id}/cancel", name="user_group_lesson_cancel")
     * @param GroupLessonManager $groupLessonManager
     * @param string $id
     * @return Response
     */
    public function cancelSubscribe(GroupLessonManager $groupLessonManager, string $id): Response
    {
        try {
            $user = $this->getUser();
            if (!$user instanceof User) {
                throw new AppException("Auth error", Response::HTTP_UNAUTHORIZED);
            }
            $groupLessonManager->cancel($user, $id);
        } catch (AppException $e) {
            throw new ServiceException($e);
        }

        return $this->redirectToRoute('user_group_lesson');
    }

    /**
     * @Route("/user/groupLessons/{id}/change/notificationType", name="user_group_lesson_change_notification_type")
     * @param Request $request
     * @param GroupLessonManager $groupLessonManager
     * @param string $id
     * @return Response
     */
    public function changeNotificationType(Request $request, GroupLessonManager $groupLessonManager, string $id): Response
    {
        try {
            $notificationType = $request->query->get('notificationType');
            $user = $this->getUser();
            if (!$user instanceof User) {
                throw new AppException("Auth error", Response::HTTP_UNAUTHORIZED);
            }
            $groupLessonManager->changeNotification($user, $id, $notificationType);
        } catch (AppException $e) {
            throw new ServiceException($e);
        }

        return $this->redirectToRoute('user_group_lesson');
    }
}