<?php

namespace App\Cards;

class CardHand
{
    private array $cards;

    /**
     * Constructs card hand.
     *
     * @param array $cards Array of cards that make up the hand. Each element in the array should be an instance of the Card class.
     * @throws \InvalidArgumentException If any element of the array is not a Card.
     */
    public function __construct(array $cards)
    {
        foreach ($cards as $card) {
            if (!$card instanceof Card) {
                throw new \InvalidArgumentException('Invalid card in the array.');
            }
        }
        $this->cards = $cards;

        echo("<br><br>");
        echo(implode(",", $this->getCardStrings()));
    }

    /**
     * Returns an array of strings representing the cards.
     *
     * @return array Array of strings representing the cards.
     */
    public function getCardStrings(): array
    {
        $cardStrings = [];
        foreach ($this->cards as $card) {
            $cardStrings[] = $card->getCardString();
        }
        return $cardStrings;
    }

}
