<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ConnexionController extends AbstractController 
{
    #[Route('/connexion', name:'connexion_form', methods: ['GET', 'POST'])]
    public function show(): Response {
        return $this->render('connexion.html.twig');
    }
}