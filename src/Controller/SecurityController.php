<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Account;
use App\Security\JWTService;
use App\Security\EmailVerifier;
use Symfony\Component\Mime\Email;
use App\Form\RegistrationFormType;
use App\Form\RegistrationUserType;
use Symfony\Component\Mime\Address;
use App\Entity\RegistrationFormData;
use App\Form\RegistrationAccountType;
use App\Repository\AccountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Security\Exception\AccountNotVerifiedException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;


class SecurityController extends AbstractController
{

    // LOG IN

    #[Route('/connexion', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {

        $error = $authenticationUtils->getLastAuthenticationError();

        return $this->render('pages/security/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'Error' => $error
        ]);
    }

    //LOGOUT
    #[Route('/deconnexion', name: 'app_logout', methods: ['GET'])]
    public function logout(): never
    {
        // Rien à ajouter
    }


    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    // EMAIL VERIFICATION
    #[Route('/verify/{token}', name: 'app_verify_email')]
    public function verifyAccount($token, JWTService $jwt, AccountRepository $accountRepository, EntityManagerInterface $entityManager): Response
    {
        // On vérifie si le token est valide, n'a pas expiré et n'a pas été modifié
        if ($jwt->isValid($token) && !$jwt->isExpired($token) && $jwt->check($token, $this->getParameter('app.jwtsecret'))) {
            // On récupère le payload
            $payload = $jwt->getPayload($token);

            // On récupère le account du token
            $account = $accountRepository->find($payload['account_id']);

            // On vérifie que l'utilisateur existe et n'a pas encore activé son compte
            if ($account && !$account->IsVerified()) {

                $account->setIsVerified(true);
                $entityManager->persist($account);
                $entityManager->flush();

                $this->addFlash('success', 'Utilisateur activé');

                return $this->redirectToRoute('app_login');
            }
        }

        // Ici un problème se pose dans le token
        $this->addFlash('danger', 'Le token est invalide ou a expiré');

        return $this->redirectToRoute('app_home');
    }
}
