<?

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="equalish.css?v=<? echo md5_file("./equalish.css") ?>">
    <script src="equalish.js?v=<? echo md5_file("./equalish.js") ?>"></script>
    <title>Equalish</title>
</head>

<body>
    <div class="block" id="orig-block" onclick="selectBlock(this.id)" hidden></div>
    <div class="wrong-x-box hide" id="orig_wrong">X</div>

    <header>
        <h1>Equalish</h1>
        <button onclick="document.getElementById('rules').showModal()">Rules</button>
    </header>
    <dialog id="rules">
        <h2>
            How to Play
        </h2>
        <p>
            The goal of <span class="fancy">Equalish</span> is to have both sides of the equal sign (=) equal the same
            value!
        <ul>
            <li>You must use all of the blocks from the bottom 'bank'</li>
            <li>If needed, round to the first decimal place</li>
            <li>The 'Check' button will turn green when you can submit your answer.</li>
        </ul>
        </p>
    </dialog>
    <main>
        <div class="equations row">
            <div id="left-equation" class="row">

            </div>
            <div class="equal-sign">
                =
            </div>
            <div id="right-equation" class="row">

            </div>
        </div>
        <button id="nextButton" disabled class="green mid" type="button" onclick="checkEquations()">Check</button>
        <div id="bank" class="bank row">

        </div>
    </main>

    <div class="floating_card" id="win_float" hidden>
        <p class="float_title">You Win!!!</p>
        <p class="float_description"></p>
    </div>
    <div class="mid row hide" id="wrong">
    </div>
</body>

</html>