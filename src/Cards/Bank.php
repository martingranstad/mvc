<?php

namespace App\Cards;

use App\Cards\Player;

class Bank extends Player
{
    protected bool $playing;

    /**
     * Constructor for Player class.
     *
     * @param CardHand $cardHand The players hand of cards.
     */
    public function __construct(protected CardHand $cardHand)
    {
        $this->playing = true;
    }

    /**
     * The bank will play and return the total points of the hand.
     *
     * @param DeckOfCards $deck The deck of cards to draw from.
     *
     * @return int Total points of the hand.
     */
    public function play($deck): int
    {
        while ($this->cardHand->getTotalPoints() < 17) {
            $this->cardHand->addCard($deck->drawCards(1)[0]);
        }
        $this->stopPlaying();
        return $this->cardHand->getTotalPoints();
    }
}
