<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Account;
use App\Security\JWTService;
use App\Security\EmailVerifier;
use App\Form\RegistrationFormType;
use Symfony\Component\Mime\Address;
use App\Entity\RegistrationFormData;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
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
        MailerInterface $mailer, JWTService $jwt
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

            //$encodedToken = urlencode($token);

            // Generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation(
                'app_verify_email',
                $account,
                (new TemplatedEmail())
                    ->from(new Address('no-reply@tickets.com', 'No-Reply'))
                    ->to($account->getEmail())
                    ->subject('Tickets - Please verify your email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
                    ->context([
                        'account' => $account,
                        'token' => $token
                        ])
            );

            return $this->redirectToRoute('app_login');
        }

        return $this->render('pages/register/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
