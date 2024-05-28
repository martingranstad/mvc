<?php

namespace App\Project;

use App\Project\Card;
use App\Project\CardHand;
use App\Project\Player;
use PHPUnit\Framework\TestCase;

class PlayerTest extends TestCase
{
    public function testReset(): void
    {
        $cardHand = new CardHand();
        $player = new Player($cardHand);

        $player->reset();

        $this->assertEquals(0, $player->getTotalPoints());
        $this->assertTrue($player->isPlaying());
        $this->assertEquals([], $player->getCardStrings());
    }

    public function testStopPlaying(): void
    {
        $cardHand = new CardHand();
        $player = new Player($cardHand);

        $player->stopPlaying();

        $this->assertFalse($player->isPlaying());
        $this->assertEquals($cardHand->getTotalPoints(), $player->getTotalPoints());
    }

    public function testAddCard(): void
    {
        $cardHand = new CardHand();
        $player = new Player($cardHand);

        $card = new Card('S', '1');
        $player->addCard($card);

        $this->assertEquals([$card->getCardString()], $player->getCardStrings());
        $this->assertEquals($card->getPoints(), $player->getTotalPoints());
    }
}
