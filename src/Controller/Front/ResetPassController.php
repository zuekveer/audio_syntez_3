<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResetPassController extends AbstractController
{
    #[Route('/reset', name: 'reset-password')]
    public function index(): Response
    {
        return $this->render('resetPassword.html.twig');
    }
}
