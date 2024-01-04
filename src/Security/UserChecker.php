<?php

namespace App\Security;

use App\Security\JWTService;
use App\Service\EmailService;
use App\Entity\Account as AppUser;
use App\Repository\AccountRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;


class UserChecker implements UserCheckerInterface

{
    private $email;
    private $jwt;
    private $accountRepository;
    private $jwtSecret;
    
    public function __construct(EmailService $email, JWTService $jwt, AccountRepository $accountRepository, ParameterBagInterface $parameterBag) 
    {
        $this->email = $email;
        $this->jwt = $jwt;
        $this->accountRepository = $accountRepository;
        $this->jwtSecret = $parameterBag->get('app.jwtsecret');
    }

    public function checkPreAuth(UserInterface $account): void
    {
        if ($account instanceof AppUser && !$account->IsVerified()) {

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
            $token = $this->jwt->generate($header, $payload, $this->jwtSecret);

            // On envoie l'email de confirmation
            $this->email->sendEmail(
                'no-reply@tickets.com',
                $account->getEmail(),
                'Tickets - Please verify your email',
                'confirmation_email',
                [
                    'account' => $account,
                    'token' => $token
                ]
            );

            
            // Affiche le message à l'utilisateur
            throw new CustomUserMessageAccountStatusException('Votre compte n\'est pas activé. Un nouvel email de confirmation vous a été envoyé', [], 0, null);


        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        if (!$user instanceof AppUser) {
            return;
        }

    }
}