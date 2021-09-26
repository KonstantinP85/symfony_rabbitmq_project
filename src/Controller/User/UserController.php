<?php

declare(strict_types=1);

namespace App\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * Главная страница админа
     * @Route("/user", name="main_user_page")
     * @return Response
     */
    public function index(): Response
    {
        $profile = $this->getUser();

        return $this->render('user/index.html.twig', ['profile' => $profile]);
    }
}