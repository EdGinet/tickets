<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RegisterController extends AbstractController 
{
    #[Route('/inscription', name:'register', methods: ['GET', 'POST'])]
    public function show() : Response {
        return $this->render('register.html.twig');
    }
}