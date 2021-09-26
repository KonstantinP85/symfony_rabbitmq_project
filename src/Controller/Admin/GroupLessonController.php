<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\DtoModel\GroupLessonDtoModel;
use App\Entity\GroupLesson;
use App\Exception\AppException;
use App\Exception\ServiceException;
use App\Form\GroupLessonType;
use App\Form\MessageType;
use App\Manager\GroupLessonManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GroupLessonController extends AdminController
{
    /**
     * Список групповых занятий
     * @Route("/admin/grouplesson", name="admin_group_lesson")
     * @param GroupLessonManager $groupLessonManager
     * @return Response
     */
    public function list(GroupLessonManager $groupLessonManager): Response
    {
        $groupLessons = $groupLessonManager->getListForAdmin();

        return $this->render('admin/groupLesson.html.twig', [
            'groupLessons' => $groupLessons
        ]);
    }

    /**
     * Создание пользователя
     * @Route("/admin/grouplesson/create", name="admin_group_lesson_create")
     * @param Request $request
     * @param GroupLessonManager $groupLessonManager
     * @return Response
     */
    public function create(Request $request, GroupLessonManager $groupLessonManager): Response
    {
        $model = new GroupLessonDtoModel();
        $form = $this->createForm(GroupLessonType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $groupLessonManager->create(
                $model->title,
                $model->firstNameTrainer,
                $model->lastNameTrainer,
                $model->description,
                $model->patronymicTrainer
            );

                return $this->redirectToRoute('admin_group_lesson');
            }

        return $this->render('admin/groupLessonForm.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Удаление занятия
     * @Route("/admin/grouplesson/{id}/delete/", name="admin_group_lesson_delete")
     * @param GroupLessonManager $groupLessonManager
     * @param string $id
     * @return Response
     */
    public function remove(GroupLessonManager $groupLessonManager, string $id): Response
    {
        try {
            $groupLessonManager->remove($id);
        } catch (AppException $e) {
            throw new ServiceException($e);
        }

        return $this->redirectToRoute('admin_group_lesson');
    }

    /**
     * Отправка сообщений и информация и занятии
     * @Route("/admin/grouplesson/{id}/sandMessage", name="admin_group_lesson_sand_message")
     * @param Request $request
     * @param GroupLessonManager $groupLessonManager
     * @param string $id
     * @return Response
     */
    public function sendMessage(Request $request, GroupLessonManager $groupLessonManager, string $id): Response
    {
        try {
            $form = $this->createForm(MessageType::class);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $smsMessage = $form->get('smsMessage')->getData();
                if (is_string($smsMessage)) $groupLessonManager->sendSmsMessage($smsMessage, $id);
                $emailMessage = $form->get('emailMessage')->getData();
                if (is_string($emailMessage)) $groupLessonManager->sendEmailMessage($emailMessage, $id);

                return $this->redirectToRoute('admin_group_lesson');
            }
        } catch (AppException $e) {
            throw new ServiceException($e);
        }

        return $this->render('admin/groupLessonMessage.html.twig', [
            'form' => $form->createView(),
            'groupLesson' => $this->getDoctrine()->getRepository(GroupLesson::class)->find($id),
        ]);
    }
}