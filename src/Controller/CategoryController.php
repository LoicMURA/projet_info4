<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Service\FileService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/categorie")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("/new", name="category_new", methods={"GET","POST"})
     */
    public function new(
        Request $request,
        EntityManagerInterface $manager,
        FileService $fileService
    ): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fileService->uploadFile($category, $this->getParameter('upload_directory'));

            $manager->persist($category);
            $manager->flush();

            return $this->redirectToRoute('admin');
        }

        return $this->render('category/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="category_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Category $category, FileService $fileService): Response
    {
        $oldFile = $category->getPicture();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($category->getPicture() == null) {
                $category->setPicture($oldFile);
            } else {
                $dir = $this->getParameter('upload_directory');
                $fileService->removeFile($oldFile, $dir);
                $fileService->uploadFile($category, $dir);
            }
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('admin');
        }

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="category_delete", methods={"DELETE"})
     */
    public function delete(
        Category $category,
        EntityManagerInterface $manager,
        ProductRepository $productRepository,
        FileService $fileService
    ): Response
    {
        $products = $productRepository->findBy(['category' => $category]);
        $uploadDirectory = $this->getParameter('upload_directory');

        foreach ($products as $product) {
            $fileService->removeFile($product, $uploadDirectory);
            $manager->remove($product);
        }

        $fileService->removeFile($category, $uploadDirectory);
        $manager->remove($category);
        $manager->flush();

        return $this->json([
            'message' => 'La catégorie a bien été supprimé',
            'code' => 200
        ]);
    }
}
