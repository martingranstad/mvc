<?php

namespace App\Cards;

use InvalidArgumentException;

class Player
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
     * Get the string representations of the players hand.
     *
     * @return array<string> Array of strings representing the cards.
     */
    public function getCardStrings(): array
    {
        return $this->cardHand->getCardStrings();
    }

    /**
     * Get whether the player is playing.
     *
     * @return bool True if the player is playing, false otherwise.
     */
    public function isPlaying(): bool
    {
        return $this->playing;
    }

    /**
     * Stop the player from playing.
     */
    public function stopPlaying(): void
    {
        $this->playing = false;
    }

    /**
     * Add a card to the players hand.
     *
     * @param Card $card The card to add to the players hand.
     */
    public function addCard(Card $card): void
    {
        $this->cardHand->addCard($card);
    }

    /**
     * Get the total points of the players hand.
     *
     * @return int Total points of the players hand.
     */
    public function getTotalPoints(): int
    {
        return $this->cardHand->getTotalPoints();
    }
}
