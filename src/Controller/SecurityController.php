<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Account;
use App\Form\RegistrationFormType;
use App\Form\RegistrationUserType;
use App\Entity\RegistrationFormData;
use App\Form\RegistrationAccountType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityController extends AbstractController
{
    #[Route('/connexion', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {

        $error = $authenticationUtils->getLastAuthenticationError();
        return $this->render('pages/security/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'Error' => $error
        ]);
    }

    #[Route('/deconnexion', name: 'app_logout', methods: ['GET'])]
    public function logout(): never
    {
        //Nothing to do here...
    }


    // private EmailVerifier $emailVerifier;

    /* public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    } */

    #[Route('/inscription', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $formData = new RegistrationFormData;
        $account = new Account();
        $user = new User();


        $form = $this->createForm(RegistrationFormType::class, $formData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $formData = $form->getData();

            $account = $formData->getAccount();
            $user = $formData->getUser();

            $email = $account->getEmail();
            $plainPassword = $account->getPlainPassword();

            // encode the plain password
            $account->setPassword($userPasswordHasher->hashPassword($account, $plainPassword))
                    ->setUser($user)
                    ->setRoles(['ROLE_USER']);

            $entityManager->persist($account);
            $entityManager->flush();

            // generate a signed url and email it to the user
            /* $this->emailVerifier->sendEmailConfirmation('app_verify_email', $account,
                (new TemplatedEmail())
                    ->from(new Address('edwinginet@gmail.com', 'Ed'))
                    ->to($account->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            ); */
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_login');
        }

        return $this->render('pages/register/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists

        // @TODO INSTALLER email verifier bundle
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_register');
    }
    
}
