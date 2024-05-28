<?php

namespace App\Project;

use App\Project\DeckOfCards;
use PHPUnit\Framework\TestCase;

class DeckOfCardsTest extends TestCase
{
    public function testGetCardStrings(): void
    {
        $deck = new DeckOfCards();
        $cards = $deck->getCardStrings();

        $this->assertCount(52, $cards);
    }
    public function testShuffleDeck(): void
    {
        $deck = new DeckOfCards();
        $originalOrder = $deck->getCardStrings();

        $deck->shuffleDeck();
        $shuffledOrder = $deck->getCardStrings();

        $this->assertNotEquals($originalOrder, $shuffledOrder);
    }

    public function testGetNumCards(): void
    {
        $deck = new DeckOfCards();
        $numCards = $deck->getNumCards();

        $this->assertEquals(52, $numCards);
    }

    public function testDrawCards(): void
    {
        $deck = new DeckOfCards();
        $numCardsToDraw = 5;

        $drawnCards = $deck->drawCards($numCardsToDraw);

        $this->assertCount($numCardsToDraw, $drawnCards);
        $this->assertCount(52 - $numCardsToDraw, $deck->getCardStrings());
    }
}
