<?php

use App\Cards\Card;
use App\Cards\JokerDeckOfCards;
use PHPUnit\Framework\TestCase;

class JokerDeckOfCardsTest extends TestCase
{
    public function testConstructorWithDefaultNumJokers(): void
    {
        $deck = new JokerDeckOfCards();
        $cards = $deck->getCardStrings();

        $this->assertCount(54, $cards);

        // Assert that the last 2 cards are jokers
        $this->assertEquals("[🃏]", $cards[52]);
        $this->assertEquals("[🃏]", $cards[53]);
    }

    public function testConstructorWithCustomNumJokers(): void
    {
        $numJokers = 4;
        $deck = new JokerDeckOfCards($numJokers);
        $cards = $deck->getCardStrings();

        $this->assertCount(52 + $numJokers, $cards);

        // Assert that the last four cards are jokers
        for ($i = 52; $i < 52 + $numJokers; $i++) {
            $this->assertEquals("[🃏]", $cards[$i]);
        }
    }
}
