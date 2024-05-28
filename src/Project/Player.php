<?php

namespace App\Project;

use InvalidArgumentException;

class Player
{
    protected bool $playing;
    protected int $totalPoints;
    protected int $bet;
    protected bool $betting;

    /**
     * Constructor for Player class.
     *
     * @param CardHand $cardHand The players hand of cards.
     */
    public function __construct(protected CardHand $cardHand)
    {
        $this->totalPoints = 0;
        $this->playing = true;
        $this->bet = 0;
        $this->betting = true;
    }

    /**
     * Stop the player from betting.
     *
     * @return void
     */
    public function stopBetting(): void
    {
        $this->betting = false;
    }

    /**
     * Add a bet to the player.
     *
     * @param int $bet The bet to add.
     *
     * @return void
     */
    public function addBet(int $bet): void
    {
        $this->bet += $bet;
    }

    /**
     * Get whether the player is betting.
     *
     * @return bool True if the player is betting, false otherwise.
     */
    public function isBetting(): bool
    {
        return $this->betting;
    }

    /**
     * Get the bet of the player.
     *
     * @return int The bet of the player.
     */
    public function getBet(): int
    {
        return $this->bet;
    }

    /**
     * Reset player
     */
    public function reset(): void
    {
        $this->cardHand = new CardHand();
        $this->totalPoints = 0;
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
        $this->totalPoints = $this->getTotalPoints();
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
        $this->totalPoints = $this->getTotalPoints();
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
