<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/registration', name: 'registration')]
    public function index(): Response
    {
        return $this->render('registration.html.twig');
    }

    #[Route('/personal-account', name: 'personal-account')]
    public function success(): Response
    {
        return $this->render('personalAccount.html.twig');
    }
}
