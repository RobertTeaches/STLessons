<?php
    require_once('db_credentials.php');
    $servername = "localhost";
    $database = "u202760652_MoodleLogins";
    $username = "u202760652_admin";
    $password = "MADatbp123!";

    $secret;
    
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $secret = $_POST["secret"];
    }

    $conn = mysqli_connect($servername, $username, $password, $database);
    if(!$conn)
    {
        die("Connection Failed: ".mysqli_connect_error());
    }

    $sql = 'SELECT * FROM `licenses` WHERE secret_phrase="'.$secret.'"';
    $res = $conn->query($sql);

    if($res->num_rows > 0)
    {
        echo "yes";
    }
    else
    {
        echo "no";
    }

    //echo "Connected Successfuly".$secret;
    mysqli_close($conn);
?>