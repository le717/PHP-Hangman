<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hangman</title>

  <!--[if IE]>
    <script src="//cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.2/html5shiv.min.js"></script>
  <![endif]-->

  <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Open+Sans:300,400">
  <link rel="stylesheet" href="css/style.min.css">
</head>

<body>
  <header>
    <img alt="Hangman logo" width="110" height="110" src="img/header.png">
    <h1>Hangman</h1>
    <p>How about a friendly game of Hangman? :)</p>
  </header>

  <section class="hangman">
    <img id="leg-l" data-img-id="6" width="75" height="75" src="img/leg-l.png">
    <img id="leg-r" data-img-id="5" width="75" height="75" src="img/leg-r.png">
    <img id="arm-l" data-img-id="4" width="75" height="75" src="img/arm-l.png">
    <img id="arm-r" data-img-id="3" width="75" height="75" src="img/arm-r.png">
    <img id="head" data-img-id="1" width="75" height="75" src="img/head.png">
    <img id="body" data-img-id="2" width="75" height="75" src="img/body.png">
    <img id="gallows" width="200" height="400" src="img/gallows.png">
  </section>

  <section id="area-hint">
    <h2>Hint</h2>
    <p></p>
  </section>

  <section id="area-message">
    <p></p>
  </section>

  <section id="area-word">
    <h2></h2>
    <p></p>
  </section>

  <form>
    <label><input type="text" id="guess" name="guess" autocomplete="off" maxlength="1"></label>
    <label><input type="submit" id="submit" name="submit" value="Guess!"></label>
  </form>

  <footer>
    <div>Created 2015 <a target="_blank" href="https://twitter.com/_le717">Caleb Ely</a> <span class="breaker"></span> CIST&nbsp;2351&nbsp;-&nbsp;PHP Programming I
    <span class="breaker"></span> <a target="_blank" href="http://www.southeasterntech.edu">Southeastern&nbsp;Technical&nbsp;College</a></div>
  </footer>

  <script async src="js/microajax.min.js"></script>
  <script async src="js/hangman.min.js"></script>
</body>
</html>
