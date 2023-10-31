<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function index(): Response
    {
        return $this->render('pages/register/register.html.twig', [
            'controller_name' => 'RegisterController',
        ]);
    }
}
