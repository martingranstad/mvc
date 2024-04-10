<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CardGameControllerJson
{
    #[Route("/api/deck", methods: ['GET'])]
    public function jsonDeck(
        SessionInterface $session
    ): Response
    {
        $deck = $session->get("deck");
        if ($deck) {
            $deck->sortDeck();
            $data = [
                'deck' => $deck->getCardStrings(),
            ];
        } else {
            $data = [
                'error' => 'No deck found',
            ];
        }

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    #[Route("/api/deck/shuffle", methods: ['POST', 'GET'])]
    public function jsonDeckShuffle(
        SessionInterface $session
    ): Response
    {
        $deck = $session->get("deck");
        if ($deck) {
            $deck->shuffleDeck();
            $session->set("deck", $deck);
            $data = [
                'deck' => $deck->getCardStrings(),
            ];
        } else {
            $data = [
                'error' => "No deck in session",
            ];
        }

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    #[Route("/api/deck/draw", methods: ['POST', 'GET'])]
    public function jsonDeckDraw(
        SessionInterface $session
    ): Response
    {
        $deck = $session->get("deck");
        if ($deck) {
            $card = $deck->drawCards(1)[0];

            $data = [
                "card" => $card->getCardString(),
                "Card left in deck" => $deck->getNumCards(),
            ];


        } else {
            $data = [
                'error' => $session->all(),
            ];
        }

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    #[Route("/api/deck/draw/{number}", methods: ['POST', 'GET'])]
    public function jsonDeckDrawMultiple(
        SessionInterface $session,
        int $number
    ): Response
    {
        $deck = $session->get("deck");
        if ($deck) {
            $cards = $deck->drawCards($number);
            $card_strings = [];
            foreach ($cards as $card) {
                $card_strings[] = $card->getCardString();
            }

            $data = [
                "Drawn cards" => $card_strings,
                "Card left in deck" => $deck->getNumCards(),
            ];

        } else {
            $data = [
                'error' => $session->all(),
            ];
        }

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }
}
