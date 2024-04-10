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
    ): Response
    {

        $data = [
            'session' => var_export($session->all(), true),
        ];

        return $this->render('session.html.twig', $data);
    }

    #[Route("/session/delete", name: "session-delete")]
    public function sessionDelete(
        SessionInterface $session
    ): Response
    {
        $session->clear();

        $this->addFlash(
            'notice',
            'Nu är sessionen raderad!'
        );

        return $this->redirectToRoute("session");
    }

    #[Route("/card", name: "card")]
    public function card(): Response
    {
        $card = new Card("", 0);
        $card2 = new Card("H", 5);
        $card3 = new Card("C", 13);
        $cardHand = new CardHand([$card, $card2, $card3]);
        $deck= new JokerDeckOfCards();

        return $this->render("card.html.twig");
    }
}
