<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;

class Connexion extends AbstractController 
{
    #[Route('/qui_sommes_nous', name:'about_us', methods: ['GET'])]
    public function show(): Response {
        return $this->render('about_us.html.twig');
    }
}