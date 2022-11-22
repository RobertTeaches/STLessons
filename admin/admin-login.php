<?php
    require_once('../db_credentials.php');
    $pass;
    if($_SERVER['REQUEST_METHOD'] === 'POST')
    {
        $pass = $_POST["password"];
    }
    if(!$pass) return;
    include "../functions/log-auth-attempt.php";
    $db = mysqli_connect(DB_SERVER, ADMIN_DB_USER, DB_PASS, ADMIN_DB_NAME);
    logAttempt("n/a", $pass, $db);    

    $sql = 'SELECT * FROM `passwords` WHERE password ="'.$pass.'"';
    $result = $db->query($sql);
    if($result->num_rows > 0)
    {
        echo "100";
        setcookie("password", $pass, time() + (60 * 60), "/admin");
    }
    else{
        echo "200";
    }
?>