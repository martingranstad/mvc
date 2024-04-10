<?php

namespace App\Cards;

use App\Cards\Card;
class DeckOfCards
{
    private $cards;

    public function __construct()
    {
        $this->cards = [];

        $suits = array_keys(Card::SUITS);
        $values = array_keys(Card::VALUES);

        foreach ($suits as $suit) {
            foreach ($values as $value) {
                if (!($value == 0 || $suit == "")){
                    $this->cards[] = new Card($suit, $value);
                }
            }
        }
    }

    /**
     * Returns an array of strings representing the cards.
     *
     * @return array Array of strings representing the cards.
     */
    public function getCardStrings(): array {
        $cardStrings = [];
        foreach ($this->cards as $card) {
            $cardStrings[] = $card->getCardString();
        }
        return $cardStrings;
    }


}