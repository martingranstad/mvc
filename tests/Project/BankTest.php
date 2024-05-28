<?php

namespace App\Project;

use App\Project\Bank;
use App\Project\Card;
use App\Project\CardHand;
use App\Project\DeckOfCards;
use PHPUnit\Framework\TestCase;

class BankTest extends TestCase
{
    public function testPlayAndReturnPointsNotPlaying(): void
    {
        $cardHand = new CardHand();
        $deck = new DeckOfCards();
        $bank = new Bank($cardHand);

        // Test when the bank is not playing
        $bank->stopPlaying();
        $cardHand->addCard(new Card('S', 1));
        $cardHand->addCard(new Card('S', 13));// All face cards = 10 points
        $this->assertEquals(11, $bank->playAndReturnPoints($deck));
    }
    public function testPlayAndReturnPointsCardHandLessThan17(): void
    {
        $cardHand = new CardHand();
        $deck = new DeckOfCards();
        $bank = new Bank($cardHand);


        $cardHand->addCard(new Card('S', 1));
        $cardHand->addCard(new Card('S', 13));
        $this->assertTrue($bank->playAndReturnPoints($deck) >= 17);
    }
    public function testPlayAndReturnPointsCardHandMoreThan17(): void
    {
        $cardHand = new CardHand();
        $deck = new DeckOfCards();
        $bank = new Bank($cardHand);


        $cardHand->addCard(new Card('S', 5));
        $cardHand->addCard(new Card('S', 13));
        $cardHand->addCard(new Card('S', 3));
        $this->assertEquals(18, $bank->playAndReturnPoints($deck));
    }
}
