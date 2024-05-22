<?php

namespace App\Project;

use InvalidArgumentException;

class BlackJackGame
{
    private bool $gameOver;
    private string $message;
    
    /**
     * Constructor for the class representing a game of Blackjack.
     *
     * @param Player $player The player
     * @param Bank $bank The bank
     * @param DeckOfCards $deck The deck of cards to draw from.
     */
    public function __construct(protected Player $player, protected Bank $bank, protected DeckOfCards $deck)
    {
        $this->gameOver = false;
        $this->message = "";
        $deck->shuffleDeck();
        $player->reset();
        $bank->reset();
    }

    /**
     * Play a game of Blackjack.
     *
     * @return array{message: string, playerHand: array<string>, bankHand: array<string>}|null The result of the game. If the player is still playing it returns null.
     */
    public function playGame(): array|null
    {
        if ($this->gameOver) {
            return array(
                "message" => $this->message,
                "playerHand" => $this->player->getCardStrings(),
                "bankHand" => $this->bank->getCardStrings()
            );
        }
        
        if ($this->player->getTotalPoints() > 21) {
            $this->gameOver = true;
            $this->message = "You got over 21 and lost!";
            return array(
                "message" => $this->message,
                "playerHand" => $this->player->getCardStrings(),
                "bankHand" => $this->bank->getCardStrings()
            );
        }
        
        if (!$this->player->isPlaying()) {
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
        
        if ($bankPoints > 21) {
            $this->message = "The bank got over 21 and you won!";
            return array(
                "message" => $this->message,
                "playerHand" => $this->player->getCardStrings(),
                "bankHand" => $this->bank->getCardStrings()
            );
        }
        
        if ($bankPoints >= $this->player->getTotalPoints()) {
            $this->message = "The bank won!";
            return array(
                "message" => $this->message,
                "playerHand" => $this->player->getCardStrings(),
                "bankHand" => $this->bank->getCardStrings()
            );
        }
        
        $this->message = "You had more points than the bank and won!";
        return array(
            "message" => $this->message,
            "playerHand" => $this->player->getCardStrings(),
            "bankHand" => $this->bank->getCardStrings()
        );
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
     * @return string Game over message.
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Gets the player's score and hand.
     *
     * @return array{playerHand: array<string>, playerScore: int} The player's score and hand.
     */
    public function getPlayerHand(): array
    {
        return array(
            "playerHand" => $this->player->getCardStrings(),
            "playerScore" => $this->player->getTotalPoints()
        );
    }

    /**
     * Draws a card from the deck and adds it to the player's hand.
     *
     * @return void
     */
    public function givePlayerCard(): void
    {
        $this->player->addCard($this->deck->drawCards(1)[0]);
        $this->message = "You drew a card!";
    }

    /**
     * Stops the player from playing.
     *
     * @return void
     */
    public function stopPlayerPlaying(): void
    {
        $this->player->stopPlaying();
        $this->message = "You stopped playing, now it's the bank's turn!";
    }

    /**
     * Get game state.
     *
     * @return array{playerHand: array<string>, playerScore: int, bankHand: array<string>, bankScore: int, gameOver: bool, message: string} The game state.
     */
    public function getGameState(): array
    {
        return array(
            "playerHand" => $this->player->getCardStrings(),
            "playerScore" => $this->player->getTotalPoints(),
            "bankHand" => $this->bank->getCardStrings(),
            "bankScore" => $this->bank->getTotalPoints(),
            "gameOver" => $this->gameOver,
            "message" => $this->message
        );
    }
}
