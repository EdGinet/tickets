<?php

namespace App\Controller;

use App\Security\JWTService;
use App\Service\EmailService;
use App\Security\EmailVerifier;
use App\Form\ResetPasswordFormType;
use App\Repository\AccountRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ResetPasswordRequestFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

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

    #[Route('/mot-de-passe-oublié', name: 'forgot_password')]
    public function forgotPassword(Request $request, AccountRepository $accountRepository, TokenGeneratorInterface $tokenGeneratorInterface,
    EntityManagerInterface $entityManager,
    EmailService $mail): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            // On récupère le compte par son email
            $account = $accountRepository->findOneByEmail($form->get('email')->getData());

            // On vérifie si on a un compte
            if($account){
                // On génère un token de réinitialisation
                $token = $tokenGeneratorInterface->generateToken();             
                $account->setResetToken($token);
                $entityManager->persist($account);
                $entityManager->flush();

                // On génère un lien de réinitilisation du mot de passe
                $url = $this->generateUrl('reset_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

                // On crée les données du mail
                $context = [
                    'url' => $url,
                    'account' => $account
                ];

                // On envoie le mail
                $mail->sendEmail(
                    'no-reply@tickets.com',
                    $account->getEmail(),
                    'Tickets - Réinitialisation de mot de passe',
                    'password_reset',
                    $context
                );

                $this->addFlash('success', 'Email envoyé avec succès');
                return $this->redirectToRoute('app_login');
            };
            // $account est null
            $this->addFlash('error', 'Un problème est survenu');
            return $this->redirectToRoute('app_login');
            
        }

        
        return $this->render('pages/security/reset_password_request.html.twig', [
            'requestPasswordForm' => $form->createView()
        ]);
    }

    #[Route('/mot-de-passe-oublié/{token}', name: 'reset_password')]
    public function resetPassword(
        string $token,
        Request $request,
        AccountRepository $accountRepository,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher): Response
    {

        // On vérifie si le token est dans la base de données
        $account = $accountRepository->findOneByResetToken($token);

        if($account){

            $form = $this->createForm(ResetPasswordFormType::class);

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){

                // On efface le token
                $account->setResetToken("");
                $account->setPassword(
                    $passwordHasher->hashPassword(
                        $account,
                        $form->get('password')->getData()
                    )
                );
                $entityManager->persist($account);
                $entityManager->flush();

                $this->addFlash('success', 'Mot de passe modifié avec succès');
                return $this->redirectToRoute('app_login');
            }

            return $this->render('pages/security/reset_password.html.twig', [
                'passwordForm' => $form->createView()
            ]);

        }

        $this->addFlash('danger', 'Jeton invalide');
        return $this->redirectToRoute('app_login');
    }
}
