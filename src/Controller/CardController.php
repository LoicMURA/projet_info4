<?php

namespace App\Controller;

use App\Service\CardService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CardController extends AbstractController
{
    /**
     * @Route("/panier", name="card_index")
     */
    public function index(CardService $card): Response
    {
        return $this->render('card/index.html.twig', [
            'card' => $card->getContent(),
            'total' => $card->getTotal()
        ]);
    }

    /**
     * @Route("/panier/add/{id}", name="card_add", methods={"POST"})
     */
    public function add(int $id, CardService $card) {
        $card->add($id);
        return $this->json($card->getJsonContent());
    }

    /**
     * @Route("/panier/remove/{id}", name="card_remove", methods={"DELETE"})
     */
    public function remove(int $id, CardService $card) {
        $card->remove($id);
        return $this->json($card->getJsonContent(false));
    }
}
