<?php

namespace App\Controller;

use App\Repository\CommentRepository;
use App\Service\FileService;
use Knp\Component\Pager\PaginatorInterface;
use App\Entity\{Product, Comment};
use App\Form\{ProductType, CommentType};
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Response, Request};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/nos-t-shirts")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/", name="product_index", methods={"GET"})
     */
    public function index(
        ProductRepository $productRepository,
        Request $request,
        PaginatorInterface $paginator
    ): Response
    {
        $all = $productRepository->findAll();
        $datas = $paginator->paginate(
            $all,
            $request->query->getInt('page', 1),
            9
        );

        return $this->render('product/index.html.twig', [
            'products' => $datas,
        ]);
    }

    /**
     * @Route("/{id}", name="product_show", methods={"GET", "POST"}, requirements={"id"="\d+"})
     */
    public function show(
        Product $product,
        Request $request,
        EntityManagerInterface $manager,
        Security $security
    ): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setCreatedAt(new \DateTime())
                    ->setUser($security->getUser())
                    ->setProduct($product);

            $manager->persist($comment);
            $manager->flush();
        }

        return $this->render('product/show.html.twig', [
            'product' => $product,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="product_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Product $product, FileService $fileService): Response
    {
        $oldPicture = $product->getPicture();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($product->getPicture() == null) $product->setPicture($oldPicture);
            else {
                $dir = $this->getParameter('upload_directory');

                $fileService->removeFile($oldPicture, $dir);
                $fileService->uploadFile($product, $dir);
            }
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('product_show', ['id' => $product->getId()]);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="product_delete", methods={"DELETE"})
     */
    public function delete(
        Request $request,
        Product $product,
        EntityManagerInterface $manager,
        FileService $service,
        CommentRepository $commentRepository
    ): Response
    {
        $fromAdmin = preg_match('/admin|category/', $request->server->get('HTTP_REFERER'));

        $valid = true;
        if (!$fromAdmin) {
            if (!$this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {
                $valid = false;
            }
        }

        if ($valid) {
            $service->removeFile($product, $this->getParameter('upload_directory'));

            $comments = $commentRepository->findBy(['product' => $product]);

            foreach ($comments as $comment) {
                $manager->remove($comment);
            }

            $manager->remove($product);
            $manager->flush();
        }

        if ($fromAdmin) {
            return $this->json([
                'message'=> 'L\'article a bien été supprimé',
                'code' => 200
            ]);
        }
        return $this->redirectToRoute('product_index');
    }

    /**
     * @Route("/new", name="product_new", methods={"GET","POST"})
     */
    public function new(
        Request $request,
        EntityManagerInterface $manager,
        FileService $fileService
    ): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Upload the file
            $fileService->uploadFile($product, $this->getParameter('upload_directory'));

            $manager->persist($product);
            $manager->flush();

            return $this->redirectToRoute('product_show', ["id" => $product->getId()]);
        }

        return $this->render('product/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
