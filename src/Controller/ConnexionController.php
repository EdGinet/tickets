<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;

class Connexion extends AbstractController 
{
    #[Route('/connexion', name:'connexion_form', methods: ['GET', 'POST'])]
    public function show(): Response {
        return $this->render('connexion.html.twig');
    }
}