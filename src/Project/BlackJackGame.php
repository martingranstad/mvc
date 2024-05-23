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
    
    /**
     * Constructor for the class representing a game of Blackjack.
     *
     * @param array<Player> $players The players
     * @param Bank $bank The bank
     * @param DeckOfCards $deck The deck of cards to draw from.
     * @throws InvalidArgumentException If the number of players is not between 1 and 3.
     */
    public function __construct(array $players, Bank $bank, DeckOfCards $deck)
    {
        if (count($players) < 1 || count($players) > 3) {
            throw new InvalidArgumentException("The number of players must be between 1 and 3.");
        }
        $this->players = $players;
        $this->bank = $bank;

        $this->gameOver = false;
        $this->deck = $deck;
        $this->deck->shuffleDeck();

        var_dump($this->players);
        
        foreach ($this->players as $player) {
            $this->messages[] = "";
            $this->bustedPlayers[] = false;
            $player->reset();
        }
        
        $this->bank->reset();
    }

    /**
     * Play a game of Blackjack.
     *
     * @return array{message: string, playerHand: array<string>, bankHand: array<string>}|null The result of the game. If the player is still playing it returns null.
     */
    public function playGame(): array|null
    {
        $playersPlaying = false;

        if ($this->gameOver) {
            return array(
                "messages" => $this->messages,
                "playerHands" => $this->getPlayerHands(),
                "bankHand" => $this->bank->getCardStrings()
            );
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
        
        return null;
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
            if ($this->bustedPlayers[$i]) {
                $this->messages[$i] = "You got over 21 and lost!";
                $returnArray = array(
                    "messages" => $this->messages,
                    "playerHands" => $this->getPlayerHands(),
                    "bankHand" => $this->bank->getCardStrings()
                );
            }
            else if ($bankPoints > 21) {
                $this->messages[i] = "The bank got over 21 so your hand won!";
                $returnArray = array(
                    "messages" => $this->messages,
                    "playerHands" => $this->getPlayerHands(),
                    "bankHand" => $this->bank->getCardStrings()
                );
            }
            else if ($bankPoints >= $this->players[$i]->getTotalPoints()) {
                $this->messages[$i] = "The bank won over this hand!";
                $returnArray = array(
                    "messages" => $this->messages,
                    "playerHands" => $this->getPlayerHands(),
                    "bankHand" => $this->bank->getCardStrings()
                );
            }
            else{
                $this->messages[$i] = "This hand had more points than the bank and won!";
                $returnArray = array(
                    "messages" => $this->messages,
                    "playerHands" => $this->getPlayerHands(),
                    "bankHand" => $this->bank->getCardStrings()
                );
            }
        }
        
        

        return $returnArray;
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
     * Gets the player's score and hand.
     *
     * @return array{playerHand: array<string>, playerScore: int} The player's score and hand.
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
        $this->players[$playerId]->addCard($this->deck->drawCards(1)[0]);
        $this->message = "You drew a card!";
    }

    /**
     * Stops the player from playing.
     *
     * @return void
     */
    public function stopPlayerPlaying(int $playerId): void
    {
        $this->players[$playerId]->stopPlaying();
        $this->message = "You stopped playing the hand";
    }

    /**
     * Get game state.
     *
     * @return array{playerHand: array<string>, playerScore: int, bankHand: array<string>, bankScore: int, gameOver: bool, message: string} The game state.
     */
    public function getGameState(): array
    {
        return array(
            "playerHands" => $this->getPlayerHands(),
            "bankHand" => $this->bank->getCardStrings(),
            "bankScore" => $this->bank->getTotalPoints(),
            "gameOver" => $this->gameOver,
            "messages" => $this->messages
        );
    }
}
