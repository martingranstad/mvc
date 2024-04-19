<?php

namespace App\Cards;

use App\Cards\Card;
use InvalidArgumentException;

class CardHand
{
    /**
     * @var Card[]
     */
    private array $cards;

    /**
     * Constructs card hand.
     *
     * @param array<Card> $cards Array of cards that make up the hand. Each element in the array should be an instance of the Card class.
     * @throws \InvalidArgumentException If any element of the array is not a Card.
     */
    public function __construct(array $cards = [])
    {
        if (!empty($cards)) {
            foreach ($cards as $card) {
                if (!$card instanceof Card) {
                    throw new InvalidArgumentException('Invalid card in the array.');
                }
            }
        }
        $this->cards = $cards;
    }

    /**
     * Returns an array of strings representing the cards.
     *
     * @return array<string> Array of strings representing the cards.
     */
    public function getCardStrings(): array
    {
        $cardStrings = [];
        foreach ($this->cards as $card) {
            $cardStrings[] = $card->getCardString();
        }
        return $cardStrings;
    }

    /**
     * Returns the total points of the hand.
     *
     * @return int Total points of the hand.
     */
    public function getTotalPoints(): int
    {
        $totalPoints = 0;
        foreach ($this->cards as $card) {
            $totalPoints += $card->getPoints();
        }
        return $totalPoints;
    }

    /**
     * Adds a card to the hand.
     *
     * @param Card $card Card to add to the hand.
     */
    public function addCard(Card $card): void
    {
        $this->cards[] = $card;
    }
}
