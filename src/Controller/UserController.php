<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface as Encoder;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{
    /**
     * @Route("/inscription", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request, EntityManagerInterface $manager, Encoder $encoder): Response
    {
        $user = new User();
        $user->setRole('ROLE_USER');

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);

            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('user_login');
        }

        return $this->render('user/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/connexion", name="user_login")
     */
    public function login(AuthenticationUtils $authentification): Response
    {
        $error = $authentification->getLastAuthenticationError();
        $lastUsername = $authentification->getLastUsername();

        return $this->render('user/login.html.twig', [
            'error' => $error,
            'username' => $lastUsername
        ]);
    }

    /**
     * @Route("/deconnexion", name="user_logout")
     */
    public function logout() {
        // Handled by symfony
    }
}
