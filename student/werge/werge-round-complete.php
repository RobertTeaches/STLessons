<?php
    include "../../db_credentials.php";
    $dba = mysqli_connect(DB_SERVER, ADMIN_DB_USER, DB_PASS, ADMIN_DB_NAME);
    $db = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

    $username = $_COOKIE["username"];
    $guessWord = $_POST["guessWord"];
    $leftHint = $_POST["leftHint"];
    $rightHint = $_POST["rightHint"];
    $numOfGuesses = $_POST["numOfGuesses"];
    $guesses = $_POST["guesses"];

    if(!$username){
        echo 201;
        return;
    }
    if(!$guessWord || !$leftHint || !$rightHint || !$numOfGuesses){
        echo 202;
        return;
    }

    $roundResult = [
        "word"=>$guessWord,
        "lHint"=>$leftHint,
        "rHint"=>$rightHint,
        "guessNum"=>$numOfGuesses,
        "guesses"=>$guesses
    ];
    $rJson = json_encode($roundResult);
    $time = time();
    $sql = "INSERT INTO `werge_rounds`  (`user_name`, `round_result`, `time`) VALUES (?, ?, ?)";
    $prepared = $db->prepare($sql);
    if(!$prepared){
        echo 300;
        return;
    }
    $prepared->bind_param("ssi", $username, $rJson, $time);
    if(!$prepared){
        echo 301;
        return;
    }
    $prepared->execute();
    echo 100;
?>