<?php

use App\Cards\Bank;
use App\Cards\Card;
use App\Cards\CardHand;
use App\Cards\DeckOfCards;
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
        $cardHand->addCard(new Card('S', 13));
        $this->assertEquals(14, $bank->playAndReturnPoints($deck));
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
        $this->assertEquals(18, $bank->playAndReturnPoints($deck));
    }
}
