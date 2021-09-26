<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use App\Exception\AppException;
use App\Manager\UserManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class PasswordLinkAuthentication implements AuthenticationSuccessHandlerInterface
{

    private UrlGeneratorInterface $urlGenerator;
    /**
     * @var UserManager
     */
    private UserManager $userManager;

    /**
     * @param UrlGeneratorInterface $urlGenerator
     * @param UserManager $userManager
     */
    public function __construct(UrlGeneratorInterface $urlGenerator, UserManager $userManager)
    {
        $this->urlGenerator = $urlGenerator;
        $this->userManager = $userManager;
    }

    /**
     * @throws AppException
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token): Response
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            throw new AppException('Auth error');
        }
        $password = $request->request->get('password');
        $confirmPassword = $request->request->get('confirmPassword');
        if ($password !== null && $confirmPassword !== null) {

            if ($password !== $confirmPassword) {
                throw new AppException('Пароль и подтверждение не совпадают');
            }
            $this->userManager->createPassword($user, $password, $confirmPassword);
        }

        return new RedirectResponse($this->urlGenerator->generate('main_user_page'));
    }
}