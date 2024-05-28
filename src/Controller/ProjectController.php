<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Player as PlayerEntity;
use App\Entity\GameRecord;
use Doctrine\Persistence\ManagerRegistry;

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
    #[Route("proj/api", name: "proj-api")]
    public function api(): Response
    {
        return $this->render('project/api.html.twig');
    }


    #[Route("/proj/about/database", name: "about-project-database")]
    public function aboutProjectDatabase(): Response
    {
        return $this->render('project/about-database.html.twig');
    }


    #[Route("/proj/about", name: "about-project")]
    public function aboutProject(): Response
    {
        return $this->render('project/about.html.twig');
    }

    #[Route("/proj/game", name: "proj-game", methods: ['POST', 'GET'])]
    public function gameProject(
        SessionInterface $session,
        ManagerRegistry $doctrine
    ): Response {

        // Get the name from the post request and store it in the session
        $name = $session->get("name");
        $numHands = $session->get("numHands");
        if (!$name) {
            $name = $_POST['name'];
            $session->set("name", $name);
            $numHands = $_POST['numberOfHands'];
            $session->set("numHands", $numHands);
        }

        // Check if player is in the database, if it is get the player money otherwise create player and set money to 1000
        $player = $doctrine->getRepository(PlayerEntity::class)->findOneBy(['name' => $name]);
        if (!$player) {
            $player = new PlayerEntity();
            $player->setName($name);
            $player->setMoney(1000);
            $doctrine->getManager()->persist($player);
            $doctrine->getManager()->flush();
        }



        /** @var BlackJackGame|null $game */
        $game = $session->get("game");
        if (!$game) {
            for ($i = 0; $i < $numHands; $i++) {
                $players[] = new Player(new CardHand());
            }
            $game = new BlackJackGame(
                $players,
                new Bank(new CardHand()),
                new DeckOfCards(4)
            );
            $game->setPlayerBalance($player->getMoney());
            $session->set("game", $game);
        }

        if ($game->isGameOver()) {
            $this->addFlash(
                'notice',
                implode(', ', $gameResult['messages'])
            );

            return $this->redirectToRoute('proj-game-over');
        }

        // If initialBet is in POST request, set the bet for the player
        if ($_POST && isset($_POST['initialBet'])) {
            $game->addPlayerBet($_POST['hand'], $_POST['initialBet']);
            // Decrease player money by the bet
            $newPlayerBalance = $player->getMoney() - $_POST['initialBet'];
            $player->setMoney($newPlayerBalance);
            $doctrine->getManager()->persist($player);
            $doctrine->getManager()->flush();
            $game->setPlayerBalance($newPlayerBalance);

        }

        $gameResult = $game->playGame();
        if (!$gameResult || !is_string($gameResult)) {
            $this->addFlash(
                'notice',
                implode(', ', $gameResult['messages'])
            );
            $session->set("gameResult", $gameResult);
            return $this->redirectToRoute('proj-game-over', ["messages" => $gameResult['messages']]);
        }

        return $this->render('project/game.html.twig', array_merge($game->getGameState(), ['name' => $name, 'numHands' => $numHands]));
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

    // Post route that plays double move
    #[Route("/proj/game/double", name: "proj-game-double", methods: ['POST'])]
    public function double(
        SessionInterface $session,
        ManagerRegistry $doctrine
    ): Response {
        /** @var TwentyOneGame|null $game */
        $game = $session->get("game");
        if (!$game) {
            throw new Exception("No game in session");
        }
        $handId = $_POST['hand'];

        // Subtracts the bet from the player balance
        $player = $doctrine->getRepository(PlayerEntity::class)->findOneBy(['name' => $session->get("name")]);
        $newPlayerBalance = $player->getMoney() - $game->getPlayerBets()[$handId];
        $player->setMoney($newPlayerBalance);
        $doctrine->getManager()->persist($player);
        $doctrine->getManager()->flush();
        $game->setPlayerBalance($newPlayerBalance);

        $game->doublePlayerBet($handId);
        $this->addFlash(
            'notice',
            $game->getMessage()[$handId]
        );

        return $this->redirectToRoute("proj-game");
    }

    //Game over route that displays player and dealer cards and resets the session
    #[Route("/proj/game-over", name: "proj-game-over")]
    public function gameOver(
        SessionInterface $session,
        ManagerRegistry $doctrine
    ): Response {
        /** @var array<string>|null $gameResult */
        $gameResult = $session->get("gameResult");
        if (!$gameResult) {
            throw new Exception("No game result in session");
        }

        // Give money to the player for each hand if they are a win
        $player = $doctrine->getRepository(PlayerEntity::class)->findOneBy(['name' => $session->get("name")]);
        $newPlayerBalance = $player->getMoney();

        $difference = 0;

        foreach ($gameResult['playersWon'] as $index => $winner) {
            if ($winner) {
                $difference += $gameResult['playerBets'][$index];
                $newPlayerBalance += $gameResult['playerBets'][$index] * 2;
            } else {
                $difference -= $gameResult['playerBets'][$index];
            }
        }
        $player->setMoney($newPlayerBalance);
        $doctrine->getManager()->persist($player);
        $doctrine->getManager()->flush();

        // Updates GameRecord table with the result of the game
        $gameRecord = new GameRecord();
        $gameRecord->setPlayerName($player->getName());
        $gameRecord->setResult($difference);
        $gameRecord->setTime(new \DateTime());
        $doctrine->getManager()->persist($gameRecord);
        $doctrine->getManager()->flush();


        $session->clear();
        return $this->render('project/game-over.html.twig', $gameResult);
    }


}
