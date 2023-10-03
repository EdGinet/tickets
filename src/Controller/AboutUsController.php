<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AboutUsController extends AbstractController 
{
    #[Route('/qui_sommes_nous', name:'about_us', methods: ['GET'])]
    public function show(): Response {
        return $this->render('about_us.html.twig');
    }
}