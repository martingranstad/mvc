<?php

namespace App\Controller;

use App\Cards\Card;
use App\Cards\CardHand;
use App\Cards\DeckOfCards;
use App\Cards\Player;
use App\Cards\Bank;

use Exception;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

        /** @var DeckOfCards|null $deck */
        $deck = $session->get("deck");
        if (!$deck) {
            $deck = new DeckOfCards();
            $deck->shuffleDeck();
            $session->set("deck", $deck);
        }

        /** @var Player|null $player */
        $player = $session->get("player");

        if (!$player) {
            $player = new Player(new CardHand());
            $session->set("player", $player);
        }

        if ($player->getTotalPoints() > 21) {
            //Redirect to game over
            $this->addFlash(
                'notice',
                'Du fick över 21 och förlorade!'
            );

            return $this->redirectToRoute('game-over');
        }

        if (!$player->isPlaying()) {
            $dealer = new Bank(new CardHand());

            $dealer->play($deck);

            $session->set("dealer", $dealer);


            if ($dealer->getTotalPoints() > 21) {
                $this->addFlash(
                    'notice',
                    'Banken fick över 21 och förlorade! Grattis du vann!'
                );

                return $this->redirectToRoute('game-over');
            }
            if ($dealer->getTotalPoints() >= $player->getTotalPoints()) {
                $this->addFlash(
                    'notice',
                    'Banken fick mer eller samma som dig och vann därför. Bättre lycka nästa gång!'
                );

                return $this->redirectToRoute('game-over');
            }
            $this->addFlash(
                'notice',
                'Du fick mer poäng än banken och vann! Grattis!'
            );

            return $this->redirectToRoute('game-over');
        }

        return $this->render('twenty-one.html.twig', [
            "playerCards" => $player->getCardStrings(),
            "playerScore" => $player->getTotalPoints(),
        ]);
    }

    //Post route that adds a card from the deck in session to the player in the session
    #[Route("/twenty-one/draw-card", name: "twenty-one-draw-card", methods: ['POST'])]
    public function drawCard(
        SessionInterface $session
    ): Response {
        /** @var DeckOfCards|null $deck */
        $deck = $session->get("deck");
        if (!$deck) {
            throw new Exception("No deck in session");
        }
        /** @var Player|null $player */
        $player = $session->get("player");
        if (!$player) {
            throw new Exception("No player in session");
        }

        $player->addCard($deck->drawCards(1)[0]);
        $session->set("player", $player);
        $session->set("deck", $deck);
        $this->addFlash(
            'notice',
            'Du drog ett kort!'
        );
        return $this->redirectToRoute("twenty-one");
    }

    //Post route that stops the player from playing
    #[Route("/twenty-one/stop-playing", name: "twenty-one-stop-playing", methods: ['POST'])]
    public function stopPlaying(
        SessionInterface $session
    ): Response {
        /** @var Player|null $player */
        $player = $session->get("player");
        if (!$player) {
            throw new Exception("No player in session");
        }

        $player->stopPlaying();
        $session->set("player", $player);
        $this->addFlash(
            'notice',
            'Du slutade spela! Nu är det dealerns tur.'
        );
        return $this->redirectToRoute("twenty-one");
    }

    //Game over route that displays player and dealer cards and resets the session
    #[Route("/game-over", name: "game-over")]
    public function gameOver(
        SessionInterface $session
    ): Response {
        /** @var Player|null $player */
        $player = $session->get("player");
        /** @var Bank|null $dealer */
        $dealer = $session->get("dealer");

        $session->clear();
        return $this->render('game-over.html.twig', [
            "playerCards" => $player ? $player->getCardStrings() : [],
            "dealerCards" => $dealer ? $dealer->getCardStrings() : [],
        ]);
    }
}
