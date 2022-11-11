<?php 
require_once("./db_credentials.php");
$db = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
$username;
$password;

if($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $username = $_POST["username"];
    $password = $_POST["password"];
}

if(!$username)
{
    echo 200;
    return;
}
if(!$password)
{
    echo 201;
    return;
}

//User and pass were provided
$passGet = "SELECT `password` FROM `student_logins` WHERE `user_name` = '$username'";
$res = $db->query($passGet);
if($res && $res->num_rows > 0)
{
    $storedPass = $res->fetch_row()[0];
    if($storedPass === $password)
    {
        //Login Considered Successful
        $cookieName = "username";
        setcookie($cookieName, $username, time() + (86400 * 30), "/");
        echo 100;
    }
    else{
        echo 300;
    }
}
else{
    echo $passGet;
    echo 203;
}