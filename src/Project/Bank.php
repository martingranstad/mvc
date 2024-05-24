<?php

namespace App\Project;

use App\Project\Player;

/**
 * Bank class represents the bank in a game of Black Jack.
 * Extends the Player class.
 */
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
        parent::__construct($cardHand);
    }

    /**
     * The bank will play and return the total points of the hand.
     *
     * @param DeckOfCards $deck The deck of cards to draw from.
     *
     * @return int Total points of the hand.
     */
    public function playAndReturnPoints($deck): int
    {
        if (!$this->isPlaying()) {
            return $this->cardHand->getTotalPoints();
        }
        while ($this->cardHand->getTotalPoints() < 17) {
            $this->cardHand->addCard($deck->drawCards(1)[0]);
        }
        $this->stopPlaying();
        return $this->cardHand->getTotalPoints();
    }
}
