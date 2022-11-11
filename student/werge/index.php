<?php
$username = "x";
$username = $_COOKIE["username"];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="werge.css?v=<?php echo md5_file("werge.css")?>">
    <script src="werge.js?v=<?php echo md5_file("werge.js")?>"></script>
    <title>Werge!</title>
</head>

<body>
    <dialog id="rules">
        <h2>The Rules</h2>
        <p>Welcome to Werge! It is similar to the game Wordle, with a twist!</p>
        <p>Instead of <strong>guessing wildly</strong>, Werge actually gives you all the information you need to know <em>upfront</em>!</p>
        <p>There are two <strong>Hint Words</strong>, the <em>Left</em> and <em>Right</em> Words</p>
        <p>Each Hint word will have a number underneath it</p>
        <p>This number corresponds to how many of the letters from the <strong>Hint Word</strong> are being used in your <strong>Guess Word</strong></p>
        <img src="./Example.png" alt="Example Image showing Thank with 3 underneath, and Profile with 2 underneath">
        <p>In the above example, the word we are trying to guess will have 3 letters from THANK, and 2 letters from PROFILE</p>
        <h2>Important Rules to Remember</h2>
        <ul>
            <li>The guess word will <strong>NOT</strong> repeat letters</li>
            <li>The hint words will not contain the same <em>hints</em>. I.e if you have Bob and Snow, you know that O is not a letter</li>
            <li>Every letter from the guess word can be found in the hint words</li>
        </ul>
    </dialog>

    <div id="tooltip">
        <p>This is tooltip text</p>
    </div>

    <nav <? if (!$username) echo "hidden"?>>
        Welcome <? echo $username ?>
        <button onclick="window.open('../student-logout.php')">Logout</button>
    </nav>

    <div class="container">
        <h1>Werge</h1>
        <button onclick="showRules()" style="width: fit-content; margin-left: auto; margin-right: auto;">Rules</button>
        <div class="game">
            <div id="next-button" hidden>
                <h1>You Win!</h1>
                <button onclick="resetGame()" >Next Round</button>
            </div>
            <div class="hints">
                <div id="left-container">
                    <h1 class="hoverable" id="left-word" onclick="showToolTip(this, true)">Left Word</h1>
                    <h2 id="left-number">3</h2>
                </div>
                <div class="left-arrow" hidden><img src="./Curved_Arrow.svg.png"></div>
                <div  id="right-container">
                    <h1 class="hoverable" id="right-word" onclick="showToolTip(this, true)">Right Word</h1>
                    <h2 id="right-number">4</h2>
                </div>
                <div class="right-arrow" hidden><img src="./Curved_Arrow.svg.png"></div>
            </div>
            <div class="guesses">
                <div id="row_0" class="row">
                    <div id="box_0_0" class="letter-box hoverable">
                        
                    </div>
                </div>
            </div>
        </div>
        <div class="keyboard">
            <div class="row">
                <div class="keyboard-key" onclick="playerType(this.id)" id="Q">Q</div>
                <div class="keyboard-key" onclick="playerType(this.id)" id="W">W</div>
                <div class="keyboard-key" onclick="playerType(this.id)" id="E">E</div>
                <div class="keyboard-key" onclick="playerType(this.id)" id="R">R</div>
                <div class="keyboard-key" onclick="playerType(this.id)" id="T">T</div>
                <div class="keyboard-key" onclick="playerType(this.id)" id="Y">Y</div>
                <div class="keyboard-key" onclick="playerType(this.id)" id="U">U</div>
                <div class="keyboard-key" onclick="playerType(this.id)" id="I">I</div>
                <div class="keyboard-key" onclick="playerType(this.id)" id="O">O</div>
                <div class="keyboard-key" onclick="playerType(this.id)" id="P">P</div>
            </div>
            <div class="row">
                <div class="keyboard-spacer"></div>
                <div class="keyboard-key" onclick="playerType(this.id)" id="A">A</div>
                <div class="keyboard-key" onclick="playerType(this.id)" id="S">S</div>
                <div class="keyboard-key" onclick="playerType(this.id)" id="D">D</div>
                <div class="keyboard-key" onclick="playerType(this.id)" id="F">F</div>
                <div class="keyboard-key" onclick="playerType(this.id)" id="G">G</div>
                <div class="keyboard-key" onclick="playerType(this.id)" id="H">H</div>
                <div class="keyboard-key" onclick="playerType(this.id)" id="J">J</div>
                <div class="keyboard-key" onclick="playerType(this.id)" id="K">K</div>
                <div class="keyboard-key" onclick="playerType(this.id)" id="L">L</div>
                <div class="keyboard-spacer"></div>
            </div>
            <div class="row">
                <div class="keyboard-key wide" onclick="playerGuess()" id="Enter">Enter</div>
                <div class="keyboard-key" onclick="playerType(this.id)" id="Z">Z</div>
                <div class="keyboard-key" onclick="playerType(this.id)" id="X">X</div>
                <div class="keyboard-key" onclick="playerType(this.id)" id="C">C</div>
                <div class="keyboard-key" onclick="playerType(this.id)" id="V">V</div>
                <div class="keyboard-key" onclick="playerType(this.id)" id="B">B</div>
                <div class="keyboard-key" onclick="playerType(this.id)" id="N">N</div>
                <div class="keyboard-key" onclick="playerType(this.id)" id="M">M</div>
                <div class="keyboard-key wide" onclick="playerBackspace()" id="Backspace">Backspace</div>
            </div>
        </div>
    </div>
</body>

</html>