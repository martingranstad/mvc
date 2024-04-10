<?php

namespace App\Cards;

use App\Cards\Card;
use App\Cards\DeckOfCards;


class JokerDeckOfCards extends DeckOfCards
{
    private $cards;

    public function __construct(int $numJokers = 2)
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

        //Add jokers
        for ($i=0; $i<$numJokers; $i++) {
            $this->cards[] = new Card("", 0);
        }
    }

}