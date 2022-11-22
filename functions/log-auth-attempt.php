<?php
function getUserIpAddr()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function logAttempt($user, $pass,mysqli $db )
{

    $sql = "INSERT INTO `login_attempts`(`user_name`, `attempted_password`) VALUES (?,?)";
    $prepared = $db->prepare($sql);
    if($prepared)
    {
        $suc = $prepared->bind_param("ss", $user,$pass);
        if($suc) $prepared->execute();
    }
}