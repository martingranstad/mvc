<?php

namespace App\Cards;

use App\Cards\Card;

class DeckOfCards
{
    protected array $cards;

    public function __construct()
    {
        $this->cards = [];

        $suits = array_keys(Card::SUITS);
        $values = array_keys(Card::VALUES);

        foreach ($suits as $suit) {
            foreach ($values as $value) {
                if (!($value == 0 || $suit == "")) {
                    $this->cards[] = new Card($suit, $value);
                }
            }
        }
    }

    /**
     * Shuffles the deck of cards.
     */
    public function shuffleDeck(): void
    {
        shuffle($this->cards);
    }

    /**
     * Sort the deck of cards.
     */
    public function sortDeck(): void
    {
        sort($this->cards);
    }

    /**
     * Returns the number of cards in the deck.
     */
    public function getNumCards(): int
    {
        return count($this->cards);
    }

    /**
     * Draws a specified number of cards from the deck.
     *
     * @param int $numCards The number of cards to draw.
     * @return array Array of cards drawn.
     */
    public function drawCards(int $numCards): array
    {
        $drawnCards = [];
        for ($i = 0; $i < $numCards; $i++) {
            if (!empty($this->cards)) {
                $drawnCards[] = array_pop($this->cards);
            }
        }
        return $drawnCards;
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
