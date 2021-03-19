<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Service\Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        return $this->render('app/home.html.twig');
    }

    /**
     * @Route("/a", name="index_a")
     */
    public function abTestA(): Response
    {
        return $this->render('app/home.html.twig');
    }

    /**
     * @Route("/b", name="index_b")
     */
    public function abTestB(): Response
    {
        return $this->render('app/homeB.html.twig');
    }

    /**
     * @Route("/notre-histoire", name="a-propos")
     */
    public function aPropos(): Response
    {
        return $this->render('app/a-propos.html.twig');
    }

    /**
     * @Route("/mentions-legales", name="legals")
     */
    public function legals(): Response
    {
        return $this->render('app/mentions-legales.html.twig');
    }

    /**
     * @Route("/conditions-generales-de-vente", name="cgv")
     */
    public function cgv(): Response
    {
        return $this->render('app/cgv.html.twig');
    }

    /**
     * @Route("/nous-contacter", name="contact")
     */
    public function contact(Request $request, Mailer $mailer): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'Votre message a bien été envoyé à nos services !');

            $mailer->sendMail($contact);
        }

        return $this->render('app/contact.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function admin(CategoryRepository  $catRepo, ProductRepository $productRepo): Response
    {
        return $this->render('admin/panel.html.twig', [
            'products' => $productRepo->findBy([], ['name' => 'ASC']),
            'categories' => $catRepo->findBy([], ['name' => 'ASC'])
        ]);
    }
}
