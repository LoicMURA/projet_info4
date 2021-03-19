<?php

namespace App\Service;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CardService
{
    protected $session;
    protected $productRepository;

    public function __construct(SessionInterface $session, ProductRepository $repository)
    {
        $this->session = $session;
        $this->productRepository = $repository;
    }

    public function getCard(): array {
        return $this->session->get('card', []);
    }

    public function add(int $id, int $quantity = 1)
    {
        $card = $this->getCard();

        if (!empty($card[$id])) $card[$id] += $quantity;
        else $card[$id] = $quantity;

        $this->session->set('card', $card);
    }

    public function remove(int $id)
    {
        $card = $this->getCard();

        if (!empty($card[$id])) unset($card[$id]);

        $this->session->set('card', $card);
    }

    public function getContent(): array
    {
        $card = $this->getCard();
        $cardContent = [];

        foreach ($card as $product => $quantity) {
            $cardContent[] = [
                'product' => $this->productRepository->find($product),
                'quantity' => $quantity
            ];
        }

        return $cardContent;
    }

    public function getJsonContent(bool $add = true): array
    {
        $message = $add ? "L'article a été ajouté à votre panier" : "L'article à bien été supprimé de votre panier";
        $result = [
            'code' => 200,
            'message' => $message
        ];
        $content = $this->getContent();

        foreach ($content as $row) {
            $product = $row['product'];
            $result[] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'price' =>$product->getPrice(),
                'quantity' => $row['quantity']
            ];
        }
        $result['total'] = $this->getTotal();

        return $result;
    }

    public function getTotal(): float
    {
        $total = 0;
        $card = $this->getContent();

        foreach ($card as $row) {
            $total += $row['product']->getPrice() * $row['quantity'];
        }
        return $total;
    }
}