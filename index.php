<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Cache-Control" content="no-store" />
    <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
    <script lang="javascript" src="main.js?v=1<?php echo md5_file("main.js"); ?>"></script>
    <script lang="php" src="main.php"></script>
    <link href="main.css?v=1<?php echo md5_file("main.css"); ?>" rel="stylesheet">
</head>

<body>
    <div class="intro container" id="intro">
        <h1>Welcome to Minecraft Coding Lessons!</h1>
        <button onclick="loginButton()">Login</button>
        <button onclick="signupButton()">Sign Up</button>
    </div>

    <div class="signup_process" id="all_signup" hidden>
        <div class="secret container" id="secret_form">
            <form action="javascript:;" onsubmit="secretForm(this)">
                <h1><span class="hint-bottom" aria-label="You should have been provided this by someone in charge!">
                        Enter your Secret Phrase</span></h1>
                <label for="phrase">

                    Secret Phrase
                </label>

                <input type="text" placeholder="Your Secret Phrase" required name="phrase">

                <button>Confirm</button>
            </form>
        </div>

        <div class="signup container" id="signup" hidden>
            <form action="javascript:;" onsubmit="signupForm(this)">
                <h1>Sign-Up</h1>
                <div class="names">
                    <input type="text" placeholder="Enter First Name" name="fName" required>
                    <input type="text" placeholder="Enter Last Name" name="lName" required>
                </div>
                <div class="label-and-box">
                    <label for="uName"> Desired User Name </label>
                    <input type="text" placeholder="Enter User Name" name="uName" required>
                </div>

                <div class="label-and-box">
                    <label for="psw"> Password </label>
                    <input type="password" placeholder="Enter Password" name="psw" required>
                </div>
                <button type="submit">Sign-Up</button>
            </form>
        </div>
    </div>
    <div class="container" id="login_container" hidden>
        <div class="container">
            <form action="javascript:;" onsubmit="login(this)">
                <h1>Login</h1>
                <input type="text" placeholder="Username" required name="username">
                <input type="password" placeholder="Password" required name="password">
                <p class="errortext" id="login-password-error" hidden>Wrong Password!</p>
                <button>Confirm</button>
            </form>
        </div>

        <div class="signup container" id="signup" hidden>
            <form action="javascript:;" onsubmit="signupForm(this)">
                <h1>Sign-Up</h1>
                <div class="names">
                    <input type="text" placeholder="Enter First Name" name="fName" required>
                    <input type="text" placeholder="Enter Last Name" name="lName" required>
                </div>
                <div class="label-and-box">
                    <label for="uName"> Desired User Name </label>
                    <input type="text" placeholder="Enter User Name" name="uName" required>
                </div>

                <div class="label-and-box">
                    <label for="psw"> Password </label>
                    <input type="password" placeholder="Enter Password" name="psw" required>
                </div>
                <button type="submit">Sign-Up</button>
            </form>
        </div>
    </div>

</body>

</html>