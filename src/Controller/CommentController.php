<?php

namespace App\Controller;

use App\Entity\{Comment, Product};
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/comment")
 */
class CommentController extends AbstractController
{
    /**
     * @Route("/new/{id}", name="comment_new", methods={"POST"})
     */
    public function new(Product $product, Request $request, EntityManagerInterface $manager,
Security $security):
    Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setCreatedAt(new \DateTime())
                    ->setUser($security->getUser())
                    ->setProduct($product);

            if (strlen($comment->getContent()) > 9 && strlen($comment->getTitle()) > 7) {

            $manager->persist($comment);
            $manager->flush();

            return $this->json([
                'code' => 200,
                'message' => 'Le commentaire a bien été ajouté',
                'data' => $comment->getJsonContent()
                ]);
            }
        }

        return $this->json([
            'code' => 500,
            'message' => 'Les valeurs entrées ne sont pas valides !'
        ]);
    }

    /**
     * @Route("/{id}/edit", name="comment_edit", methods={"POST"})
     */
    public function edit(Request $request, Comment $comment): Response
    {
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $comment->isValid()) {
            $comment->setModifiedAt(new \DateTime());
            $this->getDoctrine()->getManager()->flush();

            return $this->json([
                'code' => 200,
                'message' => 'Votre commentaire a bien été modifié',
                'data' => $comment->getJsonContent()
            ]);
        }

        return $this->json([
            'code'=> 500,
            'message' => 'Votre commentaire n\'a pas pu être modifié'
        ]);
    }

    /**
     * @Route("/{id}", name="comment_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Comment $comment): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($comment);
        $entityManager->flush();
        return $this->json([
            "message"=> "Votre commentaire a bien été supprimer",
            "code" => 200
        ]);
    }
}
