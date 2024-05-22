<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Project\Card;
use App\Project\CardHand;
use App\Project\DeckOfCards;
use App\Project\Player;
use App\Project\Bank;
use App\Project\TwentyOneGame;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ProjectController extends AbstractController
{
    #[Route("/proj", name: "project")]
    public function project(): Response
    {
        return $this->render('project/proj.html.twig');
    }


    #[Route("/proj/about", name: "about-project")]
    public function aboutProject(): Response
    {
        return $this->render('project/about.html.twig');
    }

    #[Route("/proj/game", name: "proj-game", methods: ['POST', 'GET'])]
    public function gameProject(
        SessionInterface $session
    ): Response {

        // Get the name from the post request and store it in the session
        if ($_POST) {
            $name = $_POST['name'];
            $session->set("name", $name);
        } else {
            $name = $session->get("name");
        }

        /** @var TwentyOneGame|null $game */
        $game = $session->get("game");
        if (!$game) {
            $game = new TwentyOneGame(new Player(new CardHand()), new Bank(new CardHand()), new DeckOfCards(4));
            $session->set("game", $game);
        }

        if ($game->isGameOver()) {
            $this->addFlash(
                'notice',
                $game->getMessage()
            );

            return $this->redirectToRoute('proj-game-over');
        }

        $gameResult = $game->playGame();
        if ($gameResult) {
            $this->addFlash(
                'notice',
                $gameResult['message']
            );
            $session->set("gameResult", $gameResult);
            return $this->redirectToRoute('proj-game-over');
        }


        return $this->render('project/game.html.twig', array_merge($game->getPlayerHand(), ['name' => $name]));
    }

    //Post route that adds a card from the deck in session to the player in the session
    #[Route("/proj/game/draw-card", name: "proj-game-draw-card", methods: ['POST'])]
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
        return $this->redirectToRoute("proj-game");
    }

    //Post route that stops the player from playing
    #[Route("/proj/game/stop-playing", name: "proj-game-stop-playing", methods: ['POST'])]
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
        return $this->redirectToRoute("proj-game");
    }

    //Game over route that displays player and dealer cards and resets the session
    #[Route("/proj/game-over", name: "proj-game-over")]
    public function gameOver(
        SessionInterface $session
    ): Response {
        /** @var array<string>|null $gameResult */
        $gameResult = $session->get("gameResult");
        if (!$gameResult) {
            throw new Exception("No game result in session");
        }
        $session->clear();
        return $this->render('project/game-over.html.twig', $gameResult);
    }

    //Route for api/game that shows the current game state
    #[Route("/proj/api/game", name: "proj-api-game", methods: ['GET'])]
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
