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

            // encode the plain password
            $account->setPassword($userPasswordHasher->hashPassword($account, $plainPassword))
                ->setUser($user)
                ->setRoles(['ROLE_USER']);

            $entityManager->persist($account);
            $entityManager->flush();

            // On génère le JWT de l'utilisateur
            // On crée le Header
            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256'
            ];

            // On crée le Payload
            $payload = [
                'account_id' => $account->getId()
            ];

            // On génère le token
            $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

            dd($token);

            //$token = $this->tokenGenerator->generateToken();
            //$account->setTokenVerification($token);

            // generate a signed url and email it to the user
            
            /*$this->emailVerifier->sendEmailConfirmation(
                'app_verify_email',
                $account,
                (new TemplatedEmail())
                    ->from(new Address('edwinginet@gmail.com', 'Ed'))
                    ->to($account->getEmail())
                    ->subject('Tickets - Please verify your email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );*/

            return $this->redirectToRoute('app_login');
        }

        return $this->render('pages/register/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
