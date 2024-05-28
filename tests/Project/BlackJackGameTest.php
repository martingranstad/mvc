<?php

namespace App\Project;

use invalidArgumentException;

use App\Project\Bank;
use App\Project\Card;
use App\Project\DeckOfCards;
use App\Project\Player;
use App\Project\BlackJackGame;
use PHPUnit\Framework\TestCase;

class BlackJackGameTest extends TestCase
{
    public function testConstructorWithValidNumberOfPlayers(): void
    {
        $players = [
            new Player(new CardHand()),
            new Player(new CardHand()),
            new Player(new CardHand())
        ];
        $bank = new Bank(new CardHand());
        $deck = new DeckOfCards();

        $game = new BlackJackGame($players, $bank, $deck);

        $this->assertInstanceOf(BlackJackGame::class, $game);
    }

    public function testConstructorWithInvalidNumberOfPlayers(): void
    {
        $players = [
            new Player(new CardHand()),
            new Player(new CardHand()),
            new Player(new CardHand()),
            new Player(new CardHand())
        ];
        $bank = new Bank(new CardHand());
        $deck = new DeckOfCards();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("The number of players must be between 1 and 3.");

        new BlackJackGame($players, $bank, $deck);
    }

    public function testAddCardToCount(): void
    {
        $players = [
            new Player(new CardHand()),
            new Player(new CardHand()),
            new Player(new CardHand())
        ];
        $bank = new Bank(new CardHand());
        $deck = new DeckOfCards();

        $game = new BlackJackGame($players, $bank, $deck);

        $card1 = new Card('S', '7');
        $card2 = new Card('H', '13');
        $card3 = new Card('D', '2');

        $startingRunningCount = $game->getRunningCount();

        $game->addCardToCount($card1);
        $this->assertEquals(0 + $startingRunningCount, $game->getRunningCount());

        $game->addCardToCount($card2);
        $this->assertEquals(($startingRunningCount - 1), $game->getRunningCount());

        $game->addCardToCount($card3);
        $this->assertEquals($startingRunningCount, $game->getRunningCount());
    }
    public function testPlayGameWhenGameOver(): void
    {
        $players = [
            new Player(new CardHand()),
            new Player(new CardHand()),
            new Player(new CardHand())
        ];
        $bank = new Bank(new CardHand());
        $deck = new DeckOfCards();

        $game = new BlackJackGame($players, $bank, $deck);
        $game->setGameOver(true);

        $result = $game->playGame();

        $this->assertEquals($game->getGameState(), $result);
    }

    public function testPlayGameWhenNotBetting(): void
    {
        $players = [
            new Player(new CardHand()),
            new Player(new CardHand()),
            new Player(new CardHand())
        ];
        $bank = new Bank(new CardHand());
        $deck = new DeckOfCards();

        $game = new BlackJackGame($players, $bank, $deck);
        $game->setGameState('playing');

        $result = $game->playGame();

        $this->assertEquals('playing', $result);
    }

    public function testPlayGameWhenBetting(): void
    {
        $players = [
            new Player(new CardHand()),
            new Player(new CardHand()),
            new Player(new CardHand())
        ];
        $bank = new Bank(new CardHand());
        $deck = new DeckOfCards();

        $game = new BlackJackGame($players, $bank, $deck);

        $result = $game->playGame();

        $this->assertEquals('initialBetting', $result);
    }

    public function testPlayGamePlayersOver21(): void
    {
        $players = [
            new Player(new CardHand()),
        ];
        $bank = new Bank(new CardHand());
        $deck = new DeckOfCards();

        $game = new BlackJackGame($players, $bank, $deck);
        for ($i = 0; $i < 12; $i++) {
            $game->givePlayerCard(0);
        }
        $players[0]->stopPlaying();
        $players[0]->stopBetting();
        $result = $game->playGame();

        $this->assertEquals('You got over 21 and lost!', $game->getMessage()[0]);
    }
    public function testGetRunningCount(): void
    {
        $players = [
            new Player(new CardHand()),
            new Player(new CardHand()),
            new Player(new CardHand())
        ];
        $bank = new Bank(new CardHand());
        $deck = new DeckOfCards();

        $game = new BlackJackGame($players, $bank, $deck);

        $runningCount = $game->getRunningCount();

        $this->assertIsInt($runningCount);
    }

    public function testGetTrueCount(): void
    {
        $players = [
            new Player(new CardHand()),
            new Player(new CardHand()),
            new Player(new CardHand())
        ];
        $bank = new Bank(new CardHand());
        $deck = new DeckOfCards();

        $game = new BlackJackGame($players, $bank, $deck);

        $trueCount = $game->getTrueCount();

        $this->assertIsFloat($trueCount);
    }

    public function testSetGameOver(): void
    {
        $players = [
            new Player(new CardHand()),
            new Player(new CardHand()),
            new Player(new CardHand())
        ];
        $bank = new Bank(new CardHand());
        $deck = new DeckOfCards();

        $game = new BlackJackGame($players, $bank, $deck);

        $game->setGameOver(true);

        $this->assertTrue($game->isGameOver());
    }
    public function testGetPlayerBets(): void
    {
        $players = [
            new Player(new CardHand()),
            new Player(new CardHand()),
            new Player(new CardHand())
        ];
        $bank = new Bank(new CardHand());
        $deck = new DeckOfCards();

        $game = new BlackJackGame($players, $bank, $deck);

        $expectedBets = [0, 0, 0];
        $actualBets = $game->getPlayerBets();

        $this->assertEquals($expectedBets, $actualBets);
    }

    public function testSetGameState(): void
    {
        $players = [
            new Player(new CardHand()),
            new Player(new CardHand()),
            new Player(new CardHand())
        ];
        $bank = new Bank(new CardHand());
        $deck = new DeckOfCards();

        $game = new BlackJackGame($players, $bank, $deck);

        $gameState = 'playing';
        $game->setGameState($gameState);

        $this->assertEquals($gameState, $game->getGameState()["gameState"]);
    }

    public function testGetDeck(): void
    {
        $players = [
            new Player(new CardHand()),
            new Player(new CardHand()),
            new Player(new CardHand())
        ];
        $bank = new Bank(new CardHand());
        $deck = new DeckOfCards();

        $game = new BlackJackGame($players, $bank, $deck);

        $actualDeck = $game->getDeck();

        $this->assertInstanceOf(DeckOfCards::class, $actualDeck);
    }

    public function testGetPlayersPlaying(): void
    {
        $players = [
            new Player(new CardHand()),
            new Player(new CardHand()),
            new Player(new CardHand())
        ];
        $bank = new Bank(new CardHand());
        $deck = new DeckOfCards();

        $game = new BlackJackGame($players, $bank, $deck);

        $playersPlaying = $game->getPlayersPlaying();

        $this->assertIsArray($playersPlaying);
        $this->assertCount(3, $playersPlaying);
        $this->assertContainsOnly('bool', $playersPlaying);
    }

    public function testAddPlayerBet(): void
    {
        $players = [
            new Player(new CardHand()),
            new Player(new CardHand()),
            new Player(new CardHand())
        ];
        $bank = new Bank(new CardHand());
        $deck = new DeckOfCards();

        $game = new BlackJackGame($players, $bank, $deck);

        $playerId = 1;
        $bet = 100;

        $game->addPlayerBet($playerId, $bet);

        $this->assertEquals($bet, $players[$playerId]->getBet());
        $this->assertFalse($players[$playerId]->isBetting());
    }
    public function testDoublePlayerBet(): void
    {
        $players = [
            new Player(new CardHand()),
            new Player(new CardHand()),
            new Player(new CardHand())
        ];
        $bank = new Bank(new CardHand());
        $deck = new DeckOfCards();

        $game = new BlackJackGame($players, $bank, $deck);

        $playerId = 1;
        $initialBet = $players[$playerId]->getBet();
        $initialCardCount = count($players[$playerId]->getCardStrings());
        $initialPlayingStatus = $players[$playerId]->isPlaying();

        $game->doublePlayerBet($playerId);

        $this->assertEquals($initialBet * 2, $players[$playerId]->getBet());
        $this->assertCount($initialCardCount + 1, $players[$playerId]->getCardStrings());
        $this->assertFalse($players[$playerId]->isPlaying());
    }

    public function testGetGameState(): void
    {
        $players = [
            new Player(new CardHand()),
            new Player(new CardHand()),
            new Player(new CardHand())
        ];
        $bank = new Bank(new CardHand());
        $deck = new DeckOfCards();

        $game = new BlackJackGame($players, $bank, $deck);

        $this->assertEquals($game->getPlayerHands(), $game->getGameState()["playerHands"]);
    }
    public function testGetMessage(): void
    {
        $players = [
            new Player(new CardHand()),
            new Player(new CardHand()),
            new Player(new CardHand())
        ];
        $bank = new Bank(new CardHand());
        $deck = new DeckOfCards();

        $game = new BlackJackGame($players, $bank, $deck);

        $expectedMessages = ["","",""];
        $actualMessages = $game->getMessage();

        $this->assertEquals($expectedMessages, $actualMessages);
    }

    public function testSetPlayerBalance(): void
    {
        $players = [
            new Player(new CardHand()),
            new Player(new CardHand()),
            new Player(new CardHand())
        ];
        $bank = new Bank(new CardHand());
        $deck = new DeckOfCards();

        $game = new BlackJackGame($players, $bank, $deck);

        $playerBalance = 100;

        $game->setPlayerBalance($playerBalance);

        $this->assertEquals($playerBalance, $game->getGameState()["playerBalance"]);
    }
    public function testGivePlayerCard(): void
    {
        $players = [
            new Player(new CardHand()),
            new Player(new CardHand()),
            new Player(new CardHand())
        ];
        $bank = new Bank(new CardHand());
        $deck = new DeckOfCards();

        $game = new BlackJackGame($players, $bank, $deck);

        $playerId = 1;

        $initialCardCount = count($players[$playerId]->getCardStrings());

        $game->givePlayerCard($playerId);

        $this->assertCount($initialCardCount + 1, $players[$playerId]->getCardStrings());
        $this->assertEquals("You drew a card!", $game->getMessage()[$playerId]);
    }
    public function testStopPlayerPlaying(): void
    {
        $players = [
            new Player(new CardHand()),
            new Player(new CardHand()),
            new Player(new CardHand())
        ];
        $bank = new Bank(new CardHand());
        $deck = new DeckOfCards();

        $game = new BlackJackGame($players, $bank, $deck);

        $playerId = 1;

        $game->stopPlayerPlaying($playerId);

        $this->assertFalse($players[$playerId]->isPlaying());
        $this->assertEquals("You stopped playing the hand", $game->getMessage()[$playerId]);
    }

    public function testGameIsOverAfterPlayBank(): void
    {
        $players = [
            new Player(new CardHand()),
            new Player(new CardHand()),
            new Player(new CardHand())
        ];
        $bank = new Bank(new CardHand());
        $deck = new DeckOfCards();

        $game = new BlackJackGame($players, $bank, $deck);

        $result = $game->playBank();

        $this->assertEquals(true, $game->getGameState()["gameOver"]);
    }

    public function testMessagesWhenAndPlayersWonWhenBusted(): void
    {
        $players = [
            new Player(new CardHand()),
        ];
        $bank = new Bank(new CardHand());
        $deck = new DeckOfCards();

        $game = new BlackJackGame($players, $bank, $deck);
        for ($i = 0; $i < 12; $i++) {
            $game->givePlayerCard(0);
        }

        $result = $game->playBank();

        $this->assertEquals("You got over 21 and lost!", $game->getMessage()[0]);
        $this->assertEquals(false, $game->getGameState()["playersWon"][0]);
    }
}
