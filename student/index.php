<?php
    if(!isset($_COOKIE["username"]))
    {
        echo "Failed to Login";
        return;
    }
    $username = $_COOKIE["username"];
?>
<!--Student Dashboard -->
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Dashboard</title>
    <meta http-equiv="Cache-Control" content="no-store" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
    <link href="../main.css?v=1<?php echo md5_file("../main.css"); ?>" rel="stylesheet">
    <link href="student.css?v=1<?php echo md5_file("student.css"); ?>" rel="stylesheet">
    <script src="student.js?v=1<?php echo md5_file("student.js"); ?>"></script>
</head>

<body>
    <nav>
        <h3>Hello there, <?php echo $username; ?>!</h3>
        <button onclick="logOut()">Log Out</button>
    </nav>
    <div class="container horizontal">
        <div class="card minecraft">
            <h1>Launch your Minecraft Coding Lessons</h1>
            <button onclick="goToMinecraftCoding()">Go</button>
        </div>
        <div class="card bounties" style="width: fit-content;">
            <h1>Access Bounties</h1>
            <button style="margin: auto;" onclick="goToBounties()">Go</button>
        </div>
        <div class="card">
            <h1>WERGE Game!</h1>
            <button style="margin: auto;" onclick="window.location = './werge'">Go</button>
        </div>
        <div class="card">
            <h1>Equalish Game!</h1>
            <button style="margin: auto;" onclick="window.location = './equalish'">Go</button>
        </div>
    </div>
</body>

</html>