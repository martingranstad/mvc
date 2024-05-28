<?php

namespace App\Project;

use InvalidArgumentException;

class BlackJackGame
{
    private bool $gameOver;
    private array $messages;
    private array $bustedPlayers;
    private array $players;
    private $deck;
    private $bank;
    private string $gameState;
    private int $playerBalance;
    private array $playersWon;
    private int $runningCount;
    private float $trueCount;

    /**
     * Constructor for the class representing a game of Blackjack.
     *
     * @param array<Player> $players The players
     * @param Bank $bank The bank
     * @param DeckOfCards $deck The deck of cards to draw from.
     * @throws InvalidArgumentException If the number of players is not between 1 and 3.
     */
    public function __construct(array $players, Bank $bank, DeckOfCards $deck, string $gameState = "initialBetting")
    {
        if (count($players) < 1 || count($players) > 3) {
            throw new InvalidArgumentException("The number of players must be between 1 and 3.");
        }
        $this->players = $players;
        $this->bank = $bank;
        $this->gameState = $gameState;

        $this->gameOver = false;
        $this->deck = $deck;
        $this->deck->shuffleDeck();

        $this->playerBalance = 0;
        $this->playersWon = [];

        $this->runningCount = 0;
        $this->trueCount = 0;


        foreach ($this->players as $player) {
            $this->messages[] = "";
            $this->bustedPlayers[] = false;
            $player->reset();
        }

        $this->bank->reset();
        $firstCard = $this->deck->drawCards(1)[0];
        $this->addCardToCount($firstCard);
        $this->bank->addCard($firstCard);
    }

    /**
     * Add a card to the count.
     *
     * @param Card $card The card to add to the count.
     * @return void
     */
    public function addCardToCount(Card $card): void
    {
        if ($card->getPoints() >= 10) {
            $this->runningCount -= 1;
        } elseif ($card->getPoints() <= 6) {
            $this->runningCount += 1;
        }
        $this->trueCount = $this->runningCount / ($this->deck->getNumCards() / 52);
    }

    /**
     * Play a game of Blackjack.
     *
     * @return array{message: string, playerHand: array<string>, bankHand: array<string>}|null The result of the game. If the player is still playing it returns null.
     */
    public function playGame(): array|null|string
    {
        $playersPlaying = false;

        if ($this->gameOver) {
            return $this->getGameState();
        }

        // If none of the players are betting set the game state to playing
        $betting = false;
        for ($i = 0; $i < count($this->players); $i++) {
            if ($this->players[$i]->isBetting()) {
                $betting = true;
            }
        }
        if ($betting) {
            return $this->gameState;
        } else {
            $this->gameState = "playing";
        }


        for ($i = 0; $i < count($this->players); $i++) {
            if ($this->bustedPlayers[$i]) {
                continue;
            }

            $player = $this->players[$i];
            $playerPoints = $player->getTotalPoints();

            if ($playerPoints > 21) {
                $this->bustedPlayers[$i] = true;
                $this->messages[$i] = "You got over 21 and lost!";
                $player->stopPlaying();
            }
            if ($player->isPlaying()) {
                $playersPlaying = true;
            }
        }

        if (!$playersPlaying) {
            return $this->playBank();
        }

        return $this->gameState;
    }

    /**
     * Plays the bank part, sets message and game over. Returns an array with the result.
     *
     * @return array{message: string, playerHand: array<string>, bankHand: array<string>} The result of the game.
     */
    public function playBank(): array
    {
        $bankPoints = $this->bank->playAndReturnPoints($this->deck);

        $this->gameOver = true;


        $returnArray = [];
        for ($i = 0; $i < count($this->players); $i++) {
            if ($this->players[$i]->getTotalPoints() > 21) {
                $this->messages[$i] = "You got over 21 and lost!";
                $this->playersWon[$i] = false;
            } elseif ($bankPoints > 21) {
                $this->messages[$i] = "The bank got over 21 so your hand won!";
                $this->playersWon[$i] = true;
            } elseif ($bankPoints >= $this->players[$i]->getTotalPoints()) {
                $this->messages[$i] = "The bank won over this hand!";
                $this->playersWon[$i] = false;
            } else {
                $this->messages[$i] = "This hand had more points than the bank and won!";
                $this->playersWon[$i] = true;
            }
        }



        return $this->getGameState();
    }

    /**
     * Returns true if the game is over, false otherwise.
     *
     * @return bool True if the game is over, false otherwise.
     */
    public function isGameOver(): bool
    {
        return $this->gameOver;
    }

    /**
     * Return game over message.
     *
     * @return array<String> Game over messages.
     */
    public function getMessage(): array
    {
        return $this->messages;
    }

    /**
     * Set the player balance.
     *
     *
     * @param int $playerBalance The player balance.
     * @return void
     */
    public function setPlayerBalance(int $playerBalance): void
    {
        $this->playerBalance = $playerBalance;
    }

    /**
     * Gets the player's score and hand.
     *
     * @return array{array<int<0, max>, array{playerHand: mixed, playerScore: mixed}>} The player's score and hand.
     */
    public function getPlayerHands(): array
    {
        $playerHands = array();
        foreach ($this->players as $player) {
            $playerHands[] = array(
                "playerHand" => $player->getCardStrings(),
                "playerScore" => $player->getTotalPoints()
            );
        }
        return $playerHands;
    }

    /**
     * Draws a card from the deck and adds it to the player's hand.
     *
     * @return void
     */
    public function givePlayerCard(int $playerId): void
    {
        $card = $this->deck->drawCards(1)[0];
        $this->addCardToCount($card);
        $this->players[$playerId]->addCard($card);
        $this->messages[$playerId] = "You drew a card!";
    }

    /**
     * Stops the player from playing.
     *
     * @return void
     */
    public function stopPlayerPlaying(int $playerId): void
    {
        $this->players[$playerId]->stopPlaying();
        $this->messages[$playerId] = "You stopped playing the hand";
    }

    /**
     * Doubles the player's bet and draws a card and stops the player from playing.
     *
     * @return void
     */
    public function doublePlayerBet(int $playerId): void
    {
        $this->players[$playerId]->addBet($this->players[$playerId]->getBet());
        $this->players[$playerId]->addCard($this->deck->drawCards(1)[0]);
        $this->players[$playerId]->stopPlaying();
    }

    /**
     * Get game state.
     *
     * @return array{playerHands: array<string, array<string>|int>, bankHand: array<string>, bankScore: int,
     * gameOver: bool, messages: array<string>, gameState: string, playerBets: array<int>,
     * playersPlaying: array<bool>, playerBalance: int, playersWon: array<bool>, trueCount: float}.
     */
    public function getGameState(): array
    {
        return array(
            "playerHands" => $this->getPlayerHands(),
            "bankHand" => $this->bank->getCardStrings(),
            "bankScore" => $this->bank->getTotalPoints(),
            "gameOver" => $this->gameOver,
            "messages" => $this->messages,
            "gameState" => $this->gameState,
            "playerBets" => $this->getPlayerBets(),
            "playersPlaying" => $this->getPlayersPlaying(),
            "playerBalance" => $this->playerBalance,
            "playersWon" => $this->playersWon,
            "trueCount" => $this->trueCount
        );
    }

    /**
     * Get the players playing status
     *
     * @return array<bool> The players playing status.
     */
    public function getPlayersPlaying(): array
    {
        $playersPlaying = [];
        foreach ($this->players as $player) {
            $playersPlaying[] = $player->isPlaying();
        }
        return $playersPlaying;
    }


    /**
     * Add a player bet.
     *
     * @param int $playerId The player id.
     * @param int $bet The bet.
     * @return void
     */
    public function addPlayerBet(int $playerId, int $bet): void
    {
        $this->players[$playerId]->addBet($bet);
        $this->players[$playerId]->stopBetting();
    }

    /**
     * Get the player bets.
     *
     * @return array<int> The player bets.
     */
    public function getPlayerBets(): array
    {
        $bets = [];
        foreach ($this->players as $player) {
            $bets[] = $player->getBet();
        }
        return $bets;
    }

    /**
     * Set game state.
     *
     * @param string $gameState The game state.
     * @return void
     */
    public function setGameState(string $gameState): void
    {
        $this->gameState = $gameState;
    }

    /**
     * Get game deck
     *
     * @return DeckOfCards The game deck.
     */
    public function getDeck(): DeckOfCards
    {
        return $this->deck;
    }

    /**
     * Get the running count.
     *
     * @return int The running count.
     */
    public function getRunningCount(): int
    {
        return $this->runningCount;
    }

    /**
     * Get the true count.
     *
     * @return float The true count.
     */
    public function getTrueCount(): float
    {
        return $this->trueCount;
    }

    /**
     * Set the game over status.
     *
     * @param bool $gameOver The game over status.
     * @return void
     */
    public function setGameOver(bool $gameOver): void
    {
        $this->gameOver = $gameOver;
    }
}
