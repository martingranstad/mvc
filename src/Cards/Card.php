<?php

namespace App\Cards;

use InvalidArgumentException;
class Card
{
    private string $suit;
    private int $value;

    /**
     * Constructor for card class. 
     *
     * @param string $suit The suit of the card "H"=Hearts, "C"=Clubs, "D"=Diamonds, "S"=Spades
     * @param int $value The value of the card 2=2. For face cards ace=1, jack=11, queen=12, king=13
     */
    public function __construct(string $suit, int $value)
    {
        $validSuits = ['H', 'C', 'D', 'S'];

        if (!in_array($suit, $validSuits)) {
            throw new InvalidArgumentException("Invalid suit: $suit");
        }

        if (!(1 <= $value && $value <= 13)) {
            throw new InvalidArgumentException("Invalid card value: $value");
        }

        $this->suit = $suit;
        $this->value = $value;

        echo($this->getCardString());
    }

    /**
     * Get the string representation of the card.
     *
     * @return string The string representation of the card.
     */
    public function getCardString(): string
    {
        $suits = [
            'H' => '♥',
            'C' => '♣',
            'D' => '♦',
            'S' => '♠',
        ];

        $values = [
            1 => 'A',
            2 => '2',
            3 => '3',
            4 => '4',
            5 => '5',
            6 => '6',
            7 => '7',
            8 => '8',
            9 => '9',
            10 => '10',
            11 => 'J',
            12 => 'Q',
            13 => 'K',
        ];

        return "[" . $values[$this->value] . $suits[$this->suit] . "]";
    }
}