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
use App\Project\BlackJackGame;
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
            $numHands = $_POST['numberOfHands'];
        } else {
            $name = $session->get("name");
            $numHands = 1;
        }

        /** @var TwentyOneGame|null $game */
        $game = $session->get("game");
        if (!$game) {
            for ($i = 0; $i < $numHands; $i++) {
                $players[] = new Player(new CardHand());
            }
            $game = new BlackJackGame($players,
                                    new Bank(new CardHand()),
                                    new DeckOfCards(4));
            $session->set("game", $game);
        }

        if ($game->isGameOver()) {
            $this->addFlash(
                'notice',
                implode(', ', $gameResult['messages'])
            );

            return $this->redirectToRoute('proj-game-over');
        }

        $gameResult = $game->playGame();
        if ($gameResult) {
            $this->addFlash(
                'notice',
                implode(', ', $gameResult['messages'])
            );
            $session->set("gameResult", $gameResult);
            return $this->redirectToRoute('proj-game-over', ["messages" => $gameResult['messages']]);
        }


        return $this->render('project/game.html.twig', array_merge(["playerHands" => $game->getPlayerHands()], ['name' => $name, 'numHands' => $numHands]));
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
        // Get hand id from POST request
        $handId = $_POST['hand'];

        $game->givePlayerCard($handId);
        $this->addFlash(
            'notice',
            $game->getMessage()[$handId]
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
        $handId = $_POST['hand'];
        $game->stopPlayerPlaying($handId);
        $this->addFlash(
            'notice',
            $game->getMessage()[$handId]
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
