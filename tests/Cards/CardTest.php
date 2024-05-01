<?php

namespace App\Cards;

use App\Cards\Card;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Class CardTest
 * @package App\Cards
 */
class CardTest extends TestCase
{
    /**
     * Test for creating a valid card.
     */
    public function testValidCard(): void
    {
        $card = new Card('H', 2);
        $this->assertInstanceOf(Card::class, $card);
    }

    /**
     * Test for creating a card with an invalid suit.
     * @throws InvalidArgumentException
     */
    public function testInvalidSuit(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $card = new Card('X', 2);
    }

    /**
     * Test for creating a card with an invalid value.
     * @throws InvalidArgumentException
     */
    public function testInvalidValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $card = new Card('H', 15);
    }

    /**
     * Test for getting the string representation of a card.
     */
    public function testGetCardString(): void
    {
        $card = new Card('C', 11);
        $this->assertEquals('[J♣]', $card->getCardString());
    }

    /**
     * Test for setting the points of a card.
     */
    public function testSetPoints(): void
    {
        $card = new Card('D', 4);
        $card->setPoints(5);
        $this->assertEquals(5, $card->getPoints());
    }
}
