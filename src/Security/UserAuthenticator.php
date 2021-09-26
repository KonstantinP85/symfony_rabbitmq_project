<?php

declare(strict_types=1);

namespace App\Security;

use App\DataProvider\UserDataProvider;
use App\Entity\User;
use App\Exception\AppException;
use App\Manager\UserManager;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class UserAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    /**
     * @var UrlGeneratorInterface
     */
    private UrlGeneratorInterface $urlGenerator;

    /**
     * @var UserManager
     */
    private UserManager $userManager;

    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    /**
     * @param UrlGeneratorInterface $urlGenerator
     * @param UserManager $userManager
     * @param UserRepository $userRepository
     */
    public function __construct(UrlGeneratorInterface $urlGenerator, UserManager $userManager, UserRepository $userRepository)
    {
        $this->urlGenerator = $urlGenerator;
        $this->userManager= $userManager;
        $this->userRepository = $userRepository;
    }

    /**
     * @param Request $request
     * @return PassportInterface
     * @throws AppException
     */
    public function authenticate(Request $request): PassportInterface
    {
        $email = $request->request->get('email', '');

        $user = $this->userRepository->findOneBy(['email' => $email]);
        if ($user instanceof User && !$user->isActive()) {
            throw new CustomUserMessageAuthenticationException('Аккаунт заблокирован. Обратитесь к администратору');
        }
        $this->userManager->addUserLoginAttempt($user->getId());

        $request->getSession()->set(Security::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->get('_csrf_token')),
            ]
        );
    }

    /**
     * @param Request $request
     * @param TokenInterface $token
     * @param string $firewallName
     * @return Response|null
     * @throws \Exception
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            throw new CustomUserMessageAuthenticationException('Неверный логин или пароль');
        }
        $userId = $user->getId();
        $this->userManager->clearUserLoginAttempts($userId);
        if ($user->isGranted(UserDataProvider::ROLE_ADMIN)) {
            $targetPath = $this->urlGenerator->generate('main_admin_page');
        } else {
            $targetPath = $this->urlGenerator->generate('main_user_page');
        }

        return new RedirectResponse($targetPath);
    }

    /**
     * @param Request $request
     * @return string
     */
    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
