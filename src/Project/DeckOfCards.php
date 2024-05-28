<?php

namespace App\Project;

use App\Project\Card;

class DeckOfCards
{
    /**
     * @var Card[]
     */
    protected array $cards;
    private int $numberOfDecks;

    public function __construct(int $numberOfDecks = 1)
    {
        $this->cards = [];
        $this->numberOfDecks = $numberOfDecks;

        $suits = array_keys(Card::SUITS);
        $values = array_keys(Card::VALUES);

        for ($i = 0; $i < $numberOfDecks; $i++) {
            $this->createDeck($suits, $values);
        }
    }

    /**
     * Creates a deck of cards.
     *
     * @param array<string|int> $suits The suits of the cards.
     * @param array<string|int> $values The values of the cards.
     */
    private function createDeck(array $suits, array $values): void
    {
        foreach ($suits as $suit) {
            foreach ($values as $value) {
                if (!($value == 0 || $suit == "")) {
                    $this->cards[] = new Card((string)$suit, (int)$value);
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
     * @return array<Card> Array of cards drawn.
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
     * Returns the cards in the deck.
     *
     * @return array<Card> The cards in the deck.
     */
    public function getCards(): array
    {
        return $this->cards;
    }
}
