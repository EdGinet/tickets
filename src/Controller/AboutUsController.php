<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AboutUsController extends AbstractController
{
    #[Route('/qui_sommes_nous', name: 'app_about_us')]
    public function index(): Response
    {
        return $this->render('pages/about_us/about_us.html.twig', [
            'controller_name' => 'AboutUsController',
        ]);
    }
}
