<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;

class Connexion extends AbstractController 
{
    #[Route('/contact', name:'contact_us', methods: ['GET'])]
    public function show(): Response {
        return $this->render('contact.html.twig');
    }
}