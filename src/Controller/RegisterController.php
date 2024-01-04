<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Account;
use App\Security\JWTService;
use App\Security\EmailVerifier;
use App\Form\RegistrationFormType;
use App\Service\EmailService;
use App\Entity\RegistrationFormData;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class RegisterController extends AbstractController
{

    private EmailVerifier $emailVerifier;
    private TokenGeneratorInterface $tokenGenerator;

    public function __construct(EmailVerifier $emailVerifier, TokenGeneratorInterface $tokenGenerator)
    {
        $this->emailVerifier = $emailVerifier;
        $this->tokenGenerator = $tokenGenerator;
    }

    // REGISTRATION 
    #[Route('/inscription', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        EmailService $mail,
        //EmailVerifier $emailVerifier,
        JWTService $jwt
        ): Response {
        $formData = new RegistrationFormData();
        $account = new Account();
        $user = new User();


        $form = $this->createForm(RegistrationFormType::class, $formData);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $formData = $form->getData();

            $account = $formData->getAccount();
            $user = $formData->getUser();

            $plainPassword = $account->getPlainPassword();

            // Encode the plain password
            $account->setPassword($userPasswordHasher->hashPassword($account, $plainPassword))
                ->setUser($user)
                ->setRoles(['ROLE_USER']);

            $entityManager->persist($account);
            $entityManager->flush();

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
            
            return $this->redirectToRoute('app_login');
        }

        return $this->render('pages/register/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
