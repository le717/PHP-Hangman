<?php
/*
 * PHP-Hangman
 * Created 2015 Triangle717
 * <http://le717.github.io/>
 *
 * Licensed under The MIT License
 * <http://opensource.org/licenses/MIT/>
 */


  session_start();


  class Hangman {
    private $word, $hint, $guess;
    private $whiteList        = ['-', ' '];
    private $correctLetters   = [];
    private $incorrectLetters = [];

    /**
     * @public
     * Start or restore a game.
     */
    public function __construct() {
      // The word/hint is not yet generated
      if (isset($_SESSION['clearCache']) || !isset($_SESSION['gameWord'])) {
        $chosenWord = $this->selectRandomWord();
        $this->word = $chosenWord['word'];
        $this->hint = $chosenWord['hint'];
        unset($chosenWord);
        unset($_SESSION['clearCache']);

        // It was generated, restore it
      } else {
        $this->word = $_SESSION['gameWord'];
        $this->hint = $_SESSION['gameHint'];
        $this->correctLetters   = $_SESSION['correctLetters'];
        $this->incorrectLetters = $_SESSION['incorrectLetters'];
      }
    }

    /**
     * @public
     * Preserve the game hint and word.
     */
    public function __destruct() {
      $_SESSION['gameWord'] = $this->word;
      $_SESSION['gameHint'] = $this->hint;
      $_SESSION['correctLetters']   = $this->correctLetters;
      $_SESSION['incorrectLetters'] = $this->incorrectLetters;
    }

    /**
     * @private
     * Select a random word/hint pair.
     * @return array The word and its hint.
     */
    private function selectRandomWord() {
      $wordList = json_decode(file_get_contents('words/word-list.json'), true);
      $number = mt_rand(0, count($wordList) - 1);
      return $wordList[$number];
    }

    /**
     * @public
     * New game indicator.
     */
    public function newGame() {
      $_SESSION['clearCache'] = true;
    }

    /**
     * @public
     * Set the user's guessed letter.
     * @param string $guess
     */
    public function setGuess($guess) {
      $this->guess = strtolower($guess);
    }

   /**
    * @public
    * Display the word hint.
    * @return string
    */
    public function getHint() {
      return $this->hint;
    }

    /**
     * @public
     * How many incorrect guesses have been made?
     * @return integer
     */
    public function getGallows() {
      return count($this->incorrectLetters);
    }

    /**
     * @public
     * Use the correct verb tense in the word header.
     * @return string
     */
    public function getHeader() {
      return count(explode(' ', $this->word)) >= 2 ? 'The Words Are' : 'The Word Is';
    }

    public function displayWord() {
      $finalOutput = '';

      for ($i = 0; $i < iconv_strlen($this->word); $i += 1) {
        $char = $this->word[$i];

        // Blank space
        if (in_array($this->word[$i], $this->whiteList)) {
          $finalOutput .= $char;

          // Already guessed letters
        } else if (in_array(strtolower($char), $this->correctLetters)) {
          $finalOutput .= "<span>{$char}</span>";
        }

          // Unguessed letter
        else {
          $finalOutput .= '<span>&nbsp;&nbsp;</span>';
        }
      }

       return  $finalOutput;
    }

    /**
     * @public
     * Display the word structure, i.e., _ _ _ _ _.
     * @return string HTML to display.
     */
    public function displayWordStructure() {
      // Display already guessed words if available
      if (count($this->correctLetters) > 0) {
        return $this->displayWord();
      }

      $structure = '';

      for ($i = 0; $i < iconv_strlen($this->word); $i += 1) {
        // Special, non-guessable character
        if (in_array($this->word[$i], $this->whiteList)) {
          $structure .= $this->word[$i];

          // A letter
        } else {
          $structure .= "<span>&nbsp;&nbsp;</span>";
        }
      }
      return $structure;
    }

    /**
     * @public
     * Process and score a user's game
     * @return string A message for the user and the word outline.
     */
    public function processGuess() {
      // Initialize everything we need
      $messages = [];
      $wordLower = strtolower($this->word);
      $wordLen = iconv_strlen($this->word);
      $letterOccurence = substr_count($wordLower, $this->guess);
      $isCorrectGuess = (bool) $letterOccurence && !in_array($this->guess, $this->whiteList);

      // White-listed character, do not make user guess it
      for ($i = 0; $i < $wordLen; $i += 1) {
        if (in_array($wordLower[$i], $this->whiteList)) {
          $wordLen -= 1;
        }
      }

        // Already guessed this incorrect letter
      if (in_array($this->guess, $this->incorrectLetters)) {
        $messages['msg'] = 'Already an incorrect guess!';

        // Already guessed this correct letter
      } else if (in_array($this->guess, $this->correctLetters)) {
        $messages['msg'] = 'Already a correct guess!';
      }

      // Correct guess
      else if ($isCorrectGuess) {
        $messages['msg'] = 'Correct';

        // Store all occurences of the letter
        for ($i = 0; $i < $letterOccurence; $i++) {
          $this->correctLetters[] = $this->guess;
        }

        // Incorrect guess
      } else if (!$isCorrectGuess) {
        $this->incorrectLetters[] = $this->guess;
        $messages['msg'] = 'Incorrect';
      }

      // Generate word outline
      $messages['word'] = $this->displayWord();

      // You're Winner!
      if (count($this->correctLetters) === $wordLen) {
        $messages['msg'] = 'You win!';
      }

      // Game over
       else if (count($this->incorrectLetters) === 6) {
         // Completely replace the first message
        $messages['msg'] = 'Game over!';
      }

      // The number of incorrect guesses made (for displaying the gallows)
      $messages['gallows'] = $this->getGallows();

      return $messages;
    }
  }


  // Only react on POST requests
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $hangman = new Hangman();

    // Start a new game
    if (isset($_POST['pageLoad'])) {
      echo json_encode([
        'hint'    => $hangman->getHint(),
        'word'    => $hangman->displayWordStructure(),
        'header'  => $hangman->getHeader(),
        'gallows' => $hangman->getGallows()
      ]);

      // Game is in progress
    } else if (isset($_POST['guess'])) {
      $hangman->setGuess($_POST['guess']);
      $result = $hangman->processGuess();

      // A new game should be started
      if (stristr($result['msg'], 'win') || stristr($result['msg'], 'over')) {
        $hangman->newGame();
      }
      echo json_encode($result);
    }
  }
