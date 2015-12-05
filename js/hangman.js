/*jshint browser: true */
/*global microAjax */

/*
 * PHP-Hangman
 * Created 2015 Triangle717
 * <http://le717.github.io/>
 *
 * Licensed under The MIT License
 * <http://opensource.org/licenses/MIT/>
 */


(function() {
  "use strict";
  var QMessageArea  = document.querySelector("#area-message"),
      QLetterInput  = document.querySelector("form #guess"),
      QSubmitButton = document.querySelector("form #submit");

  var view = {
    displayHint: function(hint) {
      document.querySelector("#area-hint p").innerHTML = hint;
    },

    displayHeader: function(header) {
      document.querySelector("#area-word h2").innerHTML = header;
    },

    /**
     * Hide the message box. Never call this function directly!
     */
    hideMessage: function() {
      QMessageArea.classList.remove("visible");
    },

    displayMessage: function(msg) {
      document.querySelector("#area-message p").innerHTML = msg;
      QMessageArea.classList.add("visible");
    },

    displayWord: function(response) {
      // Display the updated word, clear the input box
      document.querySelector("#area-word p").innerHTML = response;
      QLetterInput.value = "";
      QLetterInput.focus();
    },

    displayGallows: function(val, pageLoad) {
      /**
       * Display the next image of the gallows.
       * @param {String} val Integer value as a string for the `data-img-id` attribute.
       */
      function _displayImage(val) {
        var QImg = document.querySelector(".hangman img[data-img-id='" + val + "']");
        QImg.style.display = "block";
      }

      // Default to displaying only one message
      if (!pageLoad) {
        pageLoad = false;
      }

      // Convert it to a number for validation
      var valInt = parseInt(val, 10);

      // Make sure it is in range
      if (valInt > 0 && valInt < 7) {

        // Display multiple images
        if (pageLoad) {
          for (var i = 1; i <= valInt; i += 1) {
            _displayImage(i);
          }

          // Display a single image
        } else {
          _displayImage(val);
        }
      }
    },

    endGame: function() {
      QLetterInput.disabled = true;
      QSubmitButton.value = "New Game";
      QSubmitButton.focus();

      // Keep the message box from being hidden
      QMessageArea.removeEventListener("transitionend", view.hideMessage);
      QSubmitButton.addEventListener("click", function(e) {
        e.preventDefault();
        window.location.replace(window.location.toString());
      });
    },
  };

  window.onload = function() {
    var options = {
      method: "POST",
      url: "hangman.php",
      data: "pageLoad=true",
      success: function(response) {
        // Display the content
        response = JSON.parse(response);
        view.displayHeader(response.header);
        view.displayWord(response.word);
        view.displayHint(response.hint);
        view.displayGallows(response.gallows, true);
      }
    };

    // Perform the request
    microAjax(options);
  };

  QSubmitButton.onclick = function(e) {
    var QGuessedLetter = QLetterInput.value;

    // Input guess
    if (!/^[a-z]$/i.test(QGuessedLetter)) {
      view.displayMessage("Invalid input");
      return false;
    }

    var options = {
      method: "POST",
      url: "hangman.php",
      data: "guess=" + QGuessedLetter,
      success: function(response) {
        // Update the message and word
        response = JSON.parse(response);
        view.displayWord(response.word);
        view.displayMessage(response.msg);

        // Update gallows area on wrong guesses
        if (/incorrect|over/i.test(response.msg)) {
          view.displayGallows(response.gallows, false);
        }

        // Game over
        if (/win|over/i.test(response.msg)) {
          view.endGame();
        }
        return true;
      }
    };

    // Perform the request
    microAjax(options);

    // Prevent the page from reloading
    e.preventDefault();
  };

  // Hide the message box upon every message display
  QMessageArea.addEventListener("transitionend", view.hideMessage);
}());
