<?php

namespace App\Controller;

use App\Cards\Card;
use App\Cards\CardHand;
use App\Cards\DeckOfCards;
use App\Cards\Player;
use App\Cards\Bank;
use App\Cards\TwentyOneGame;

use Exception;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class TwentyOneController extends AbstractController
{
    #[Route("/game", name: "game")]
    public function game(): Response
    {
        return $this->render('game.html.twig');
    }

    #[Route("/game/doc", name: "game-doc")]
    public function gameDoc(): Response
    {
        return $this->render('game-doc.html.twig');
    }
    #[Route("/twenty-one", name: "twenty-one")]
    public function twentyOne(
        SessionInterface $session
    ): Response {

        /** @var TwentyOneGame|null $game */
        $game = $session->get("game");
        if (!$game) {
            $game = new TwentyOneGame(new Player(new CardHand()), new Bank(new CardHand()), new DeckOfCards());
            $session->set("game", $game);
        }

        if ($game->isGameOver()) {
            $this->addFlash(
                'notice',
                $game->getMessage()
            );

            return $this->redirectToRoute('game-over');
        }

        $gameResult = $game->playGame();
        if ($gameResult) {
            $this->addFlash(
                'notice',
                $gameResult['message']
            );
            $session->set("gameResult", $gameResult);
            return $this->redirectToRoute('game-over');
        }

        return $this->render('twenty-one.html.twig', $game->getPlayerHand());
    }

    //Post route that adds a card from the deck in session to the player in the session
    #[Route("/twenty-one/draw-card", name: "twenty-one-draw-card", methods: ['POST'])]
    public function drawCard(
        SessionInterface $session
    ): Response {
        /** @var TwentyOneGame|null $game */
        $game = $session->get("game");
        if (!$game) {
            throw new Exception("No game in session");
        }
        $game->givePlayerCard();
        $this->addFlash(
            'notice',
            $game->getMessage()
        );
        return $this->redirectToRoute("twenty-one");
    }

    //Post route that stops the player from playing
    #[Route("/twenty-one/stop-playing", name: "twenty-one-stop-playing", methods: ['POST'])]
    public function stopPlaying(
        SessionInterface $session
    ): Response {
        /** @var TwentyOneGame|null $game */
        $game = $session->get("game");
        if (!$game) {
            throw new Exception("No game in session");
        }
        $game->stopPlayerPlaying();
        $this->addFlash(
            'notice',
            $game->getMessage()
        );
        return $this->redirectToRoute("twenty-one");
    }

    //Game over route that displays player and dealer cards and resets the session
    #[Route("/game-over", name: "game-over")]
    public function gameOver(
        SessionInterface $session
    ): Response {
        /** @var array<string>|null $gameResult */
        $gameResult = $session->get("gameResult");
        if (!$gameResult) {
            throw new Exception("No game result in session");
        }
        $session->clear();
        return $this->render('game-over.html.twig', $gameResult);
    }

    //Route for api/game that shows the current game state
    #[Route("/api/game", name: "api-game", methods: ['GET'])]
    public function apiGame(
        SessionInterface $session
    ): Response {
        /** @var TwentyOneGame|null $game */
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
}
