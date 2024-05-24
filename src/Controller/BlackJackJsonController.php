<?php

namespace App\Controller;

use App\Project\BlackJackGame;
use App\Project\DeckOfCards;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class BlackJackJsonController
{

    //Route for api/game that shows the current game state
    #[Route("/proj/api/game", name: "proj-api-game", methods: ['GET'])]
    public function apiGame(
        SessionInterface $session
    ): Response {
        /** @var BlackJackGame|null $game */
        $game = $session->get("game");
        if (!$game) {
            throw new Exception("No game in session");
        }
        $data = $game->getGameState();

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }
    #[Route("proj/api/deck", methods: ['GET'])]
    public function jsonDeck(
        SessionInterface $session
    ): Response {
        /** @var BlackJackGame|null $deck */
        $game = $session->get("game");
        if ($game) {
            $deck = $game->getDeck();
            $deck->sortDeck();
            $data = [
                'deck' => $deck->getCardStrings(),
            ];
            $response = new JsonResponse($data);
            $response->setEncodingOptions(
                $response->getEncodingOptions() | JSON_PRETTY_PRINT
            );
            return $response;
        }
        $data = [
            'error' => 'No deck found',
        ];

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    #[Route("proj/api/deck/shuffle", name: "proj-api-deck-shuffle", methods: ['POST'])]
    public function jsonDeckShuffle(
        SessionInterface $session
    ): Response {
        /** @var BlackJackGame|null $deck */
        $game = $session->get("game");
        if ($game) {
            $deck = $game->getDeck();
            $deck->shuffleDeck();
            $session->set("deck", $deck);
            $data = [
                'deck' => $deck->getCardStrings(),
            ];
            $response = new JsonResponse($data);
            $response->setEncodingOptions(
                $response->getEncodingOptions() | JSON_PRETTY_PRINT
            );
            return $response;
        }
        $data = [
            'error' => "No deck in session",
        ];

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    #[Route("proj/api/deck/draw", name: "proj-api-deck-draw", methods: ['POST'])]
    public function jsonDeckDraw(
        SessionInterface $session
    ): Response {
        /** @var BlackJackGame|null $deck */
        $game = $session->get("game");
        if ($game) {
            $deck = $game->getDeck();
            $card = $deck->drawCards(1)[0];

            $data = [
                "card" => $card->getCardString(),
                "Card left in deck" => $deck->getNumCards(),
            ];

            $response = new JsonResponse($data);
            $response->setEncodingOptions(
                $response->getEncodingOptions() | JSON_PRETTY_PRINT
            );
            return $response;

        }
        $data = [
            'error' => $session->all(),
        ];

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    #[Route("proj/api/deck/draw-multiple", name: "proj-api-deck-draw-multiple", methods: ['POST'])]
    public function jsonDeckDrawMultiple(
        SessionInterface $session,
        Request $request,
    ): Response {
        $number = $request->request->get('number');
        $number = (int) $number;
        /** @var BlackJackGame|null $deck */
        $game = $session->get("game");
        if ($game) {
            $deck = $game->getDeck();
            $cards = $deck->drawCards($number);
            $cardStrings = [];
            foreach ($cards as $card) {
                $cardStrings[] = $card->getCardString();
            }

            $data = [
                "Drawn cards" => $cardStrings,
                "Card left in deck" => $deck->getNumCards(),
            ];
            $response = new JsonResponse($data);
            $response->setEncodingOptions(
                $response->getEncodingOptions() | JSON_PRETTY_PRINT
            );
            return $response;

        }
        $data = [
            'error' => $session->all(),
        ];

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

 
}
