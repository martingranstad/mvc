<?php

namespace App\Project;

use InvalidArgumentException;

class Card
{
    private string $suit;
    private int $value;
    private int $points;
    public const SUITS = [
        'H' => '♥',
        'C' => '♣',
        'D' => '♦',
        'S' => '♠',
        '' => '',
    ];

    public const VALUES = [
        0 => '🃏',
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


    /**
     * Constructor for card class.
     *
     * @param string $suit The suit of the card "H"=Hearts, "C"=Clubs, "D"=Diamonds, "S"=Spades. ""=Joker
     * @param int $value The value of the card 2=2. For face cards ace=1, jack=11, queen=12, king=13. Joker=0
     */
    public function __construct(string $suit, int $value)
    {
        if (!array_key_exists($suit, self::SUITS)) {
            throw new InvalidArgumentException("Invalid suit: $suit");
        }

        if (!(0 <= $value && $value <= 13)) {
            throw new InvalidArgumentException("Invalid card value: $value");
        }

        $this->suit = $suit;
        $this->value = $value;
        $this->points = $value;

        echo($this->getCardString());
    }

    /**
     * Get the string representation of the card.
     *
     * @return string The string representation of the card.
     */
    public function getCardString(): string
    {
        return "[" . self::VALUES[$this->value] . self::SUITS[$this->suit] . "]";
    }

    /**
     * Set the points of the card.
     *
     * @param int $points The points to set.
     *
     * @return void
     */
    public function setPoints(int $points): void
    {
        $this->points = $points;
    }

    /**
     * Get the points of the card.
     *
     * @return int The points of the card.
     */
    public function getPoints(): int
    {
        return $this->points;
    }
}
