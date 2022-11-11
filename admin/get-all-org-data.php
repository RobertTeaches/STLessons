<?php
require_once("../db_credentials.php");

$pass;
if($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $pass = $_POST["password"];
}
if(!$pass)
{
    echo "200";
    return;
}


$db = mysqli_connect(DB_SERVER, ADMIN_DB_USER, DB_PASS, ADMIN_DB_NAME);
$sql = "SELECT acess FROM `passwords` WHERE password ='".$pass."' LIMIT 1";
$res = $db->query($sql);
$access;
if ($res)
{
    $row = $res->fetch_row();
    $access = $row[0];
    if(!$access)
    {
        echo "300";
        return;
    }
    if($access !== "1")
    {
        echo "350";
        return;
    }
}

$db_license = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

$sql = "SELECT * FROM `licenses`";

$res = $db_license->query($sql);

if($res)
{
    $data = array();
    while($row = $res->fetch_row()){
        $orgsql = "SELECT `password` FROM `passwords` WHERE `phrase`=\"$row[3]\"";
        $r = $db->query($orgsql);
        $org_pas = "no admin password";
        if($r->num_rows > 0)
        {
            $org_pas = $r->fetch_row()[0];
        }
        $org_data = [
            "name" => $row[0],
            "limit" => $row[1],
            "usage" => $row[2],
            "phrase" => $row[3],
            "date" => $row[4],
            "email" => $row[5],
            "password" => $org_pas
        ];
        array_push($data, $org_data);
    }

    echo json_encode($data);
    return;
}

echo "200";