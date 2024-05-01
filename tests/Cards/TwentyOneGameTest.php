<?php

namespace App\Cards;

use App\Cards\Bank;
use App\Cards\Card;
use App\Cards\DeckOfCards;
use App\Cards\Player;
use App\Cards\TwentyOneGame;
use PHPUnit\Framework\TestCase;

class TwentyOneGameTest extends TestCase
{
    public function testPlayGamePlayerBusts(): void
    {
        $player = new Player(new CardHand());
        $bank = new Bank(new CardHand());
        $deck = new DeckOfCards();
        $game = new TwentyOneGame($player, $bank, $deck);

        // Set up player's hand
        $player->addCard(new Card('S', 13));
        $player->addCard(new Card('S', 13));

        $result = $game->playGame();

        $this->assertTrue($game->isGameOver());
        $this->assertArrayHasKey('message', $result);
        $this->assertEquals("You got over 21 and lost!", $result['message']);
        $this->assertEquals(["[K♠]", "[K♠]"], $result['playerHand']);
        $this->assertEquals([], $result['bankHand']);
    }

    public function testPlayGamePlayerIsPlaying(): void
    {
        $player = new Player(new CardHand());
        $bank = new Bank(new CardHand());
        $deck = new DeckOfCards();
        $game = new TwentyOneGame($player, $bank, $deck);

        $result = $game->playGame();

        $this->assertNull($result);
    }

    public function testPlayGameBankBusts(): void
    {
        $player = new Player(new CardHand());
        $bank = new Bank(new CardHand());
        $deck = new DeckOfCards();
        $game = new TwentyOneGame($player, $bank, $deck);

        // Set up player's hand
        $player->addCard(new Card('S', 13));
        $player->addCard(new Card('S', 8));
        $player->stopPlaying();
        // Set up bank's hand
        $bank->addCard(new Card('S', 13));
        $bank->addCard(new Card('S', 13));

        $result = $game->playGame();

        $this->assertTrue($game->isGameOver(), "game is not over");
        $this->assertEquals("The bank got over 21 and you won!", $result['message']);
        $this->assertEquals(["[K♠]", "[8♠]"], $result['playerHand']);
        $this->assertEquals(["[K♠]", "[K♠]"], $result['bankHand']);
    }

    public function testPlayGameBankWins(): void
    {
        $player = new Player(new CardHand());
        $bank = new Bank(new CardHand());
        $deck = new DeckOfCards();
        $game = new TwentyOneGame($player, $bank, $deck);

        // Set up player's hand
        $player->addCard(new Card('S', 10));
        $player->addCard(new Card('S', 8));
        $player->stopPlaying();
        // Set up bank's hand
        $bank->addCard(new Card('S', 11));
        $bank->addCard(new Card('S', 10));

        $result = $game->playGame();

        $this->assertTrue($game->isGameOver(), "game is not over");
        $this->assertEquals("The bank won!", $result['message']);
        $this->assertEquals(["[10♠]", "[8♠]"], $result['playerHand']);
        $this->assertEquals(["[J♠]", "[10♠]"], $result['bankHand']);
    }

    public function testPlayGamePlayerWins(): void
    {
        $player = new Player(new CardHand());
        $bank = new Bank(new CardHand());
        $deck = new DeckOfCards();
        $game = new TwentyOneGame($player, $bank, $deck);

        // Set up player's hand
        $player->addCard(new Card('S', 10));
        $player->addCard(new Card('S', 8));
        $player->stopPlaying();
        // Set up bank's hand
        $bank->addCard(new Card('S', 10));
        $bank->addCard(new Card('S', 7));

        $result = $game->playGame();

        $this->assertTrue($game->isGameOver(), "game is not over");
        $this->assertEquals("You had more points then the bank and won!", $result['message']);
        $this->assertEquals(["[10♠]", "[8♠]"], $result['playerHand']);
        $this->assertEquals(["[10♠]", "[7♠]"], $result['bankHand']);
    }

    public function testGetGameState(): void
    {
        $player = new Player(new CardHand());
        $bank = new Bank(new CardHand());
        $deck = new DeckOfCards();
        $game = new TwentyOneGame($player, $bank, $deck);

        $result = $game->getGameState();

        $this->assertEquals(["message" => "", "playerHand" => [], "bankHand" => [], "bankScore" => 0, "gameOver" => false, "playerScore" => 0], $result);
    }

    public function testStopPlayerPlaying(): void
    {
        $player = new Player(new CardHand());
        $bank = new Bank(new CardHand());
        $deck = new DeckOfCards();
        $game = new TwentyOneGame($player, $bank, $deck);

        $game->stopPlayerPlaying();

        $this->assertFalse($player->isPlaying());
    }
}
