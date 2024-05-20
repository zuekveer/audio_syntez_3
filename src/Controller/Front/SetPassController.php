<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SetPassController extends AbstractController
{
    #[Route('/set-new-password', name: 'set-new-password')]
    public function index(): Response
    {
        return $this->render('setNewPassword.html.twig');
    }
}
