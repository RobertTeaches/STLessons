<?php
require_once("../db_credentials.php");

$pass;
$phrase;
if($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $pass = $_POST["password"];
    $phrase = $_POST["phrase"];
}
$db = mysqli_connect(DB_SERVER, ADMIN_DB_USER, DB_PASS, ADMIN_DB_NAME);
if(!$pass)
{
    echo "200";
    return;
}
if(!$phrase)
{
    $phrasesql = "SELECT `phrase` FROM `passwords` WHERE `password` = \"$pass\"";
    $r = $db->query($phrasesql);
    if($r->num_rows > 0)
    {
        $phrase = $r->fetch_row()[0];
    }
}
if(!$phrase)
{
    echo "201";
    return;
}


$sql = "SELECT acess FROM `passwords` WHERE password ='".$pass."' LIMIT 1";
$res = $db->query($sql);
$access;
if ($res)
{
    $row = $res->fetch_row();
    $access = $row[0];
    //3xx means access error
    if(!$access)
    {
        echo "300";
        return;
    }
    if($access !== "1" and $access !== "2")
    {
        echo "350";
        return;
    }
}

$db_license = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

$sql = "SELECT * FROM `student_logins` WHERE secret_phrase='$phrase'";
$res = $db_license->query($sql);
if($res)
{
    $data = array();
    while($row = $res->fetch_row()){
        $s_data = [
            "name"=>$row[0],
            "user"=>$row[1],
            "password"=>$row[2]
        ];
        array_push($data, $s_data);
    }
    echo json_encode($data);
    return;
}
else
{
    echo "400";
    echo $sql;
}