<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\DataProvider\UserDataProvider;
use App\DtoModel\CreateUserDtoModel;
use App\Exception\AppException;
use App\Exception\ServiceException;
use App\Form\UserType;
use App\Manager\UserManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AdminController
{
    /**
     * @Route("/admin/user", name="admin_user")
     * @param UserManager $userManager
     * @return Response
     */
    public function list(UserManager $userManager): Response
    {
        $users = $userManager->getList();

        return $this->render('admin/user.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @Route("/admin/user/create", name="admin_user_create")
     * @param Request $request
     * @param UserManager $userManager
     * @return RedirectResponse|Response
     */
    public function create(Request $request, UserManager $userManager): Response
    {
        try {
            $model = new CreateUserDtoModel();
            $form = $this->createForm(UserType::class, $model);
            $form->handleRequest($request);

            if (($form->isSubmitted()) && ($form->isValid())) {
                $image = $form->get('image')->getData();
                $filename = $image !== null ? $userManager->uploadImage($image, $this->getParameter('image_upload_directory')) : null;

                $userManager->create(
                    $model->firstName,
                    $model->lastName,
                    [UserDataProvider::ROLE_USER],
                    $model->email,
                    $model->phone,
                    $model->birthday,
                    $model->gender,
                    $model->patronymic,
                    $filename
                );

                return $this->redirectToRoute('admin_user');
            }
        } catch (AppException $e) {
            throw new ServiceException($e);
        }

        return $this->render('admin/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/user/{id}/changeStatus", name="admin_user_change_status")
     * @param UserManager $userManager
     * @param string $id
     * @return Response
     */
    public function changeStatus(UserManager $userManager, string $id): Response
    {
        try {
            $userManager->changeStatus($id);
        } catch (AppException $e) {
            throw new ServiceException($e);
        }

        return $this->redirectToRoute('admin_user');
    }
}