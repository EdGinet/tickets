<?php

namespace App\Service;

use App\Security\JWTService;
use App\Repository\AccountRepository;
use Symfony\Component\HttpFoundation\Response;



class EmailConfirmation
{
    public function resendEmail(JWTService $jwt, EmailService $mail, AccountRepository $accountRepository): Response
    {

        // Generate user's JWT
        // Create Header
        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256'
        ];

        // Create Payload
        $payload = [
            'account_id' => $account->getId()
        ];

        // Generate token
        $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

        // On envoie l'email de confirmation
        $mail->sendEmail(
            'no-reply@tickets.com',
            $account->getEmail(),
            'Tickets - Please verify your email',
            'confirmation_email',
            [
                'account' => $account,
                'token' => $token
            ]
        );
        
        $this->addFlash('success', 'Email de vérification envoyé');
        return $this->redirectToRoute('app_login');
    }
}