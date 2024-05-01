<?php

use App\Cards\Card;
use App\Cards\CardHand;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CardHandTest extends TestCase
{
    public function testGetCardStrings(): void
    {
        $cards = [
            new Card('S', 1),
            new Card('H', 3),
        ];

        $hand = new CardHand($cards);

        $expected = ['[A♠]', '[3♥]'];
        $this->assertEquals($expected, $hand->getCardStrings());
    }

    public function testGetTotalPoints(): void
    {
        $cards = [
            new Card('H', 13),
            new Card('D', 12),
            new Card('C', 5),
        ];

        $hand = new CardHand($cards);

        $expected = 30;
        $this->assertEquals($expected, $hand->getTotalPoints());
    }

    public function testAddCard(): void
    {
        $hand = new CardHand();

        $card1 = new Card('S', 1);
        $hand->addCard($card1);

        $card2 = new Card('H', 13);
        $hand->addCard($card2);

        $expected = ['[A♠]', '[K♥]'];
        $this->assertEquals($expected, $hand->getCardStrings());
    }

    public function testInvalidCard(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $cards = [
            new Card('S', 1),
            'Invalid Card',
        ];

        new CardHand($cards);
    }
}
