<?php

declare(strict_types=1);

namespace App\Controller;

use App\DtoModel\ChangePasswordDtoModel;
use App\Entity\User;
use App\Exception\AppException;
use App\Exception\ServiceException;
use App\Form\ChangePasswordType;
use App\Manager\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * Изменение пароля
     * @Route("/user/changePassword", name="user_change_password")
     */
    public function changePassword(Request $request, UserManager $userManager): Response
    {
        try {
            $user = $this->getUser();
            if (!$user instanceof User) {
                throw new AppException('Auth error', Response::HTTP_UNAUTHORIZED);
            }
            $model = new ChangePasswordDtoModel();
            $form = $this->createForm(ChangePasswordType::class, $model);
            $form->handleRequest($request);

            if (($form->isSubmitted()) && ($form->isValid())) {
                $userManager->changePassword(
                    $user,
                    $model->oldPassword,
                    $model->newPassword,
                    $model->confirmPassword
                );

                return $this->redirectToRoute('main_user_page');
            }
        } catch (AppException $e) {
            throw new ServiceException($e);
        }
        return $this->render('user/changePassword.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Страница авторизации пользователя по ссылке. Здесь же вводит пароль и подтверждение
     * @Route("/createPassword", name="create_password")
     */
    public function createPassword(Request $request): Response
    {
        $expires = $request->query->get('expires');
        $username = $request->query->get('user');
        $hash = $request->query->get('hash');

        return $this->render('security/createPassword.html.twig', [
            'expires' => $expires,
            'user' => $username,
            'hash' => $hash,
        ]);
    }
}