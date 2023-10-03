<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController 
{
    #[Route('/contact', name:'contact_us', methods: ['GET'])]
    public function show(): Response {
        return $this->render('contact.html.twig');
    }
}