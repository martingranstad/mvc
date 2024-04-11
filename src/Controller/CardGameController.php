<?php

namespace App\Controller;

use App\Cards\Card;
use App\Cards\CardHand;
use App\Cards\DeckOfCards;
use App\Cards\JokerDeckOfCards;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CardGameController extends AbstractController
{
    #[Route("/session", name: "session")]
    public function session(
        SessionInterface $session
    ): Response {

        $data = [
            'session' => var_export($session->all(), true),
        ];

        return $this->render('session.html.twig', $data);
    }

    #[Route("/session/delete", name: "session-delete")]
    public function sessionDelete(
        SessionInterface $session
    ): Response {
        $session->clear();

        $this->addFlash(
            'notice',
            'Nu är sessionen raderad!'
        );

        return $this->redirectToRoute("session");
    }

    #[Route("/card", name: "card")]
    public function card(
    ): Response {
        return $this->render("card.html.twig");
    }

    //Sorts and displays deck
    #[Route("/card/deck", name: "card-deck")]
    public function cardDeck(
        SessionInterface $session
    ): Response {
        $deck = $session->get("deck");
        if (!$deck) {
            $deck = new JokerDeckOfCards();
        }
        $deck->sortDeck();
        $session->set("deck", $deck);
        $data = [
            "deck" => $deck->getCardStrings(),
        ];

        return $this->render("cards.html.twig", $data);
    }

    //Shuffles and displays deck
    #[Route("/card/deck/shuffle", name: "card-deck-shuffle")]
    public function cardDeckShuffle(
        SessionInterface $session
    ): Response {
        $deck = $session->get("deck");
        if (!$deck) {
            $deck = new JokerDeckOfCards();
        }
        $deck->shuffleDeck();
        $session->set("deck", $deck);
        $data = [
            "deck" => $deck->getCardStrings(),
        ];

        return $this->render("cards.html.twig", $data);
    }


    //Draws a card from the deck
    #[Route("/card/deck/draw", name: "card-deck-draw")]
    public function cardDeckDraw(
        SessionInterface $session
    ): Response {
        $deck = $session->get("deck");
        if (!$deck) {
            $deck = new JokerDeckOfCards();
            $session->set("deck", $deck);
        }

        $card = $deck->drawCards(1)[0];

        $data = [
            "deck" => [$card->getCardString()],
        ];

        return $this->render("cards.html.twig", $data);
    }

    //Draws multiple cards from the deck
    #[Route("/card/deck/draw/{number}", name: "card-deck-draw-multiple")]
    public function cardDeckDrawMultiple(
        SessionInterface $session,
        int $number
    ): Response {
        $deck = $session->get("deck");
        if (!$deck) {
            $deck = new JokerDeckOfCards();
            $session->set("deck", $deck);
        }

        $cards = $deck->drawCards($number);
        $card_strings = [];
        foreach ($cards as $card) {
            $card_strings[] = $card->getCardString();
        }

        $data = [
            "deck" => $card_strings,
        ];

        return $this->render("cards.html.twig", $data);
    }
}
